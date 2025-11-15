<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\LedgerAccount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


use App\Services\VoucherTypeService;

class VoucherController extends Controller
{
    protected $voucherTypeService;

    public function __construct(VoucherTypeService $voucherTypeService)
    {
        $this->middleware(['auth', 'tenant']);
        $this->voucherTypeService = $voucherTypeService;
    }

    /**
     * Display a listing of vouchers.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Voucher::with(['voucherType', 'createdBy'])
            ->where('tenant_id', $tenant->id)
            ->latest('voucher_date');

        // Apply filters
        if ($request->filled('voucher_type')) {
            $query->where('voucher_type_id', $request->voucher_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('voucher_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('voucher_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%");
            });
        }

    $vouchers = $query->paginate(20);
    $vouchers->appends($request->query());

        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total_vouchers' => Voucher::where('tenant_id', $tenant->id)->count(),
            'draft_vouchers' => Voucher::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
            'posted_vouchers' => Voucher::where('tenant_id', $tenant->id)->where('status', 'posted')->count(),
            'total_amount' => Voucher::where('tenant_id', $tenant->id)->where('status', 'posted')->sum('total_amount'),
        ];

        $primaryVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_system_defined', true)
            ->orderByRaw("FIELD(code, 'JV', 'PV', 'RV', 'SV', 'PUR') DESC")
            ->orderBy('name')
            ->get();

        return view('tenant.accounting.vouchers.index', compact(
            'tenant',
            'vouchers',
            'voucherTypes',
            'stats',
            'primaryVoucherTypes'
        ));
    }

    /**
     * Show the form for creating a new voucher.
     */
   public function create(Request $request, Tenant $tenant, $type = null)
    {
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('affects_inventory', false)
            ->orderBy('name')
            ->get();

              // Get products for inventory-enabled vouchers
        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['primaryUnit', 'category'])
            ->orderBy('name')
            ->get();




        $selectedType = null;
        // Check route parameter first, then query parameter
        $typeCode = $type ?? $request->get('type');

        if ($typeCode) {
            $selectedType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', strtoupper($typeCode))
                ->first();

            if (!$selectedType) {
                return redirect()
                    ->route('tenant.accounting.vouchers.create', $tenant->slug)
                    ->with('error', 'Invalid voucher type specified.');
            }
        }

        $ledgerAccounts = LedgerAccount::with('accountGroup')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

       return view('tenant.accounting.vouchers.create', compact(
            'tenant',
            'voucherTypes',
            'ledgerAccounts',
            'products',
            'selectedType'
        ));
    }

    /**
     * Store a newly created voucher.
     */

    public function store(Request $request, Tenant $tenant)
    {
        // Transform contra voucher data to standard journal entry format
        if ($request->has('cv_from_account_id') && $request->has('cv_to_account_id')) {
            $entries = [
                [
                    'ledger_account_id' => $request->cv_to_account_id,
                    'particulars' => $request->cv_particulars ?? 'Contra Transfer',
                    'debit_amount' => $request->cv_transfer_amount,
                    'credit_amount' => 0,
                ],
                [
                    'ledger_account_id' => $request->cv_from_account_id,
                    'particulars' => $request->cv_particulars ?? 'Contra Transfer',
                    'debit_amount' => 0,
                    'credit_amount' => $request->cv_transfer_amount,
                ]
            ];
            $request->merge(['entries' => $entries]);
        }

        // Transform credit note data to standard journal entry format
        if ($request->has('cn_customer_account_id') && $request->has('credit_entries')) {
            $entries = [];

            // Customer account - Debit (reduces receivable)
            $entries[] = [
                'ledger_account_id' => $request->cn_customer_account_id,
                'particulars' => 'Credit Note',
                'debit_amount' => $request->cn_customer_amount,
                'credit_amount' => 0,
            ];

            // Sales/Revenue accounts - Credit
            foreach ($request->credit_entries as $entry) {
                if (!empty($entry['account_id']) && !empty($entry['amount'])) {
                    $entries[] = [
                        'ledger_account_id' => $entry['account_id'],
                        'particulars' => $entry['description'] ?? 'Credit Note',
                        'debit_amount' => 0,
                        'credit_amount' => $entry['amount'],
                    ];
                }
            }

            $request->merge(['entries' => $entries]);
        }

        // Transform debit note data to standard journal entry format
        if ($request->has('dn_customer_account_id') && $request->has('credit_entries')) {
            $entries = [];

            // Customer account - Debit (increases receivable)
            $entries[] = [
                'ledger_account_id' => $request->dn_customer_account_id,
                'particulars' => 'Debit Note',
                'debit_amount' => $request->dn_customer_amount,
                'credit_amount' => 0,
            ];

            // Additional charge accounts - Credit
            foreach ($request->credit_entries as $entry) {
                if (!empty($entry['account_id']) && !empty($entry['amount'])) {
                    $entries[] = [
                        'ledger_account_id' => $entry['account_id'],
                        'particulars' => $entry['description'] ?? 'Debit Note',
                        'debit_amount' => 0,
                        'credit_amount' => $entry['amount'],
                    ];
                }
            }

            $request->merge(['entries' => $entries]);
        }

        $request->validate([
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
            'entries.*.particulars' => 'nullable|string',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
        ]);

        // Validate that entries are balanced
        $totalDebits = collect($request->entries)->sum('debit_amount');
        $totalCredits = collect($request->entries)->sum('credit_amount');

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return back()->withErrors(['entries' => 'Voucher entries must be balanced. Total debits must equal total credits.'])->withInput();
        }

        if ($totalDebits == 0) {
            return back()->withErrors(['entries' => 'Voucher must have at least one debit and one credit entry with amounts greater than zero.'])->withInput();
        }

        // Validate that each entry has either debit or credit (not both)
        foreach ($request->entries as $index => $entry) {
            $debit = (float) ($entry['debit_amount'] ?? 0);
            $credit = (float) ($entry['credit_amount'] ?? 0);

            if ($debit > 0 && $credit > 0) {
                return back()->withErrors(["entries.{$index}" => 'An entry cannot have both debit and credit amounts.'])->withInput();
            }

            if ($debit == 0 && $credit == 0) {
                return back()->withErrors(["entries.{$index}" => 'An entry must have either a debit or credit amount.'])->withInput();
            }
        }

        $voucher = DB::transaction(function () use ($request, $tenant) {
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            // Generate voucher number
            $voucherNumber = $voucherType->getNextVoucherNumber();

            // Create voucher
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_number' => $voucherNumber,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => collect($request->entries)->sum('debit_amount'),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Create voucher entries
            foreach ($request->entries as $entryData) {
                $debitAmount = (float) ($entryData['debit_amount'] ?? 0);
                $creditAmount = (float) ($entryData['credit_amount'] ?? 0);

                if ($debitAmount > 0 || $creditAmount > 0) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entryData['ledger_account_id'],
                        'particulars' => $entryData['particulars'],
                        'debit_amount' => $debitAmount,
                        'credit_amount' => $creditAmount,
                    ]);
                }
            }

            return $voucher;
        });

        // Check if user wants to save and post
        if ($request->input('action') === 'save_and_post') {
            // Post the voucher immediately
            $voucher->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            // Update account balances for each voucher entry
            foreach ($voucher->entries as $entry) {
                $entry->updateLedgerAccountBalance();
            }

            $voucherTypeName = $voucher->voucherType->name ?? 'Voucher';

            return redirect()
                ->route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id])
                ->with('success', $voucherTypeName . ' created and posted successfully.');
        }

        $voucherTypeName = VoucherType::find($request->voucher_type_id)->name ?? 'Voucher';

        return redirect()
            ->route('tenant.accounting.vouchers.index', $tenant->slug)
            ->with('success', $voucherTypeName . ' saved as draft successfully.');
    }

    /**
     * Display the specified voucher.
     */
    public function show(Tenant $tenant, Voucher $voucher)
    {
        $voucher->load(['voucherType', 'entries.ledgerAccount.accountGroup', 'createdBy', 'updatedBy', 'postedBy']);

        return view('tenant.accounting.vouchers.show', compact('tenant', 'voucher'));
    }

    /**
     * Show the form for editing the specified voucher.
     */
    public function edit(Tenant $tenant, Voucher $voucher)
    {
        if ($voucher->status === 'posted') {
            return redirect()
                ->route('tenant.accounting.vouchers.show', [$tenant->slug, $voucher->id])
                ->with('warning', 'Posted vouchers can be edited but changes should be made carefully.');
        }

        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $ledgerAccounts = LedgerAccount::with('accountGroup')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load entries and ensure they are properly formatted
        $voucher->load('entries');

        // Prepare entries data safely
        $entriesData = $voucher->entries->map(function($entry) {
            return [
                'id' => $entry->id,
                'ledger_account_id' => $entry->ledger_account_id,
                'particulars' => $entry->particulars ?? '',
                'debit_amount' => $entry->debit_amount > 0 ? number_format($entry->debit_amount, 2, '.', '') : '',
                'credit_amount' => $entry->credit_amount > 0 ? number_format($entry->credit_amount, 2, '.', '') : '',
            ];
        })->toArray();

        return view('tenant.accounting.vouchers.edit', compact(
            'tenant',
            'voucher',
            'voucherTypes',
            'ledgerAccounts',
            'entriesData'
        ));
    }

    /**
     * Update the specified voucher.
     */
    public function update(Request $request, Tenant $tenant, Voucher $voucher)
    {
        $request->validate([
            'voucher_type_id' => 'required|exists:voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'narration' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_account_id' => 'required|exists:ledger_accounts,id',
            'entries.*.particulars' => 'nullable|string',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
        ]);

        // Validate that entries are balanced
        $totalDebits = collect($request->entries)->sum('debit_amount');
        $totalCredits = collect($request->entries)->sum('credit_amount');

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return back()->withErrors(['entries' => 'Voucher entries must be balanced.'])->withInput();
        }

        if ($totalDebits == 0) {
            return back()->withErrors(['entries' => 'Voucher must have valid entries with amounts.'])->withInput();
        }

        DB::transaction(function () use ($request, $voucher, $totalDebits) {
            // Update voucher
            $voucher->update([
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => $totalDebits,
                'updated_by' => Auth::id(),
            ]);

            // Delete existing entries
            $voucher->entries()->delete();

            // Create new entries
            foreach ($request->entries as $entryData) {
                $debitAmount = (float) ($entryData['debit_amount'] ?? 0);
                $creditAmount = (float) ($entryData['credit_amount'] ?? 0);

                if ($debitAmount > 0 || $creditAmount > 0) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entryData['ledger_account_id'],
                        'debit_amount' => $debitAmount,
                        'credit_amount' => $creditAmount,
                        'particulars' => $entryData['particulars'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id])
            ->with('success', 'Voucher updated successfully.');
    }

    /**
     * Remove the specified voucher.
     */
    public function destroy(Tenant $tenant, Voucher $voucher)
    {
        if ($voucher->status === 'posted') {
            return back()->with('error', 'Cannot delete a posted voucher. Please unpost it first.');
        }

        DB::transaction(function () use ($voucher) {
            $voucher->entries()->delete();
            $voucher->delete();
        });

        return redirect()
            ->route('tenant.accounting.vouchers.index', $tenant->slug)
            ->with('success', 'Voucher deleted successfully.');
    }

    /**
     * Post a voucher (make it final).
     */
    public function post(Tenant $tenant, Voucher $voucher)
    {
        if ($voucher->status === 'posted') {
            return back()->with('error', 'Voucher is already posted.');
        }

        // Validate voucher is balanced
        $totalDebits = $voucher->entries->sum('debit_amount');
        $totalCredits = $voucher->entries->sum('credit_amount');

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return back()->with('error', 'Cannot post an unbalanced voucher.');
        }

        if ($voucher->entries->count() < 2) {
            return back()->with('error', 'Voucher must have at least 2 entries to be posted.');
        }

        DB::transaction(function () use ($voucher) {
            // Update voucher status
            $voucher->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            // Update account balances for each voucher entry
            foreach ($voucher->entries as $entry) {
                $entry->updateLedgerAccountBalance();
            }
        });

        return back()->with('success', 'Voucher posted successfully.');
    }

    /**
     * Unpost a voucher.
     */
    public function unpost(Tenant $tenant, Voucher $voucher)
    {
        if ($voucher->status !== 'posted') {
            return back()->with('error', 'Only posted vouchers can be unposted.');
        }

        DB::transaction(function () use ($voucher) {
            // Update voucher status first
            $voucher->update([
                'status' => 'draft',
                'posted_at' => null,
                'posted_by' => null,
            ]);

            // Update account balances (the VoucherEntry model events will handle this)
            foreach ($voucher->entries as $entry) {
                $entry->updateLedgerAccountBalance();
            }
        });

        return back()->with('success', 'Voucher unposted successfully.');
    }

    /**
     * Duplicate a voucher.
     */
    public function duplicate(Tenant $tenant, Voucher $voucher)
    {
        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $ledgerAccounts = LedgerAccount::with('accountGroup')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Prepare duplicate data
        $duplicateData = [
            'voucher_type_id' => $voucher->voucher_type_id,
            'voucher_date' => now()->format('Y-m-d'),
            'reference_number' => '',
            'narration' => $voucher->narration,
            'entries' => $voucher->entries->map(function ($entry) {
                return [
                    'ledger_account_id' => $entry->ledger_account_id,
                    'particulars' => $entry->particulars,
                    'debit_amount' => $entry->debit_amount > 0 ? number_format($entry->debit_amount, 2, '.', '') : '',
                    'credit_amount' => $entry->credit_amount > 0 ? number_format($entry->credit_amount, 2, '.', '') : '',
                ];
            })->toArray()
        ];

        return view('tenant.accounting.vouchers.create', compact(
            'tenant',
            'voucherTypes',
            'ledgerAccounts',
            'duplicateData'
        ));
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request, Tenant $tenant)
    {
        $request->validate([
            'action' => 'required|in:post,unpost,delete',
            'voucher_ids' => 'required|array',
            'voucher_ids.*' => 'exists:vouchers,id',
        ]);

        $vouchers = Voucher::where('tenant_id', $tenant->id)
            ->whereIn('id', $request->voucher_ids)
            ->get();

        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($vouchers as $voucher) {
                switch ($request->action) {
                    case 'post':
                        if ($voucher->status === 'draft') {
                            $voucher->update([
                                'status' => 'posted',
                                'posted_at' => now(),
                                'posted_by' => Auth::id(),
                            ]);
                            $successCount++;
                        }
                        break;

                    case 'unpost':
                        if ($voucher->status === 'posted') {
                            $voucher->update([
                                'status' => 'draft',
                                'posted_at' => null,
                                'posted_by' => null,
                            ]);
                            $successCount++;
                        }
                        break;

                    case 'delete':
                        if ($voucher->status === 'draft') {
                            $voucher->entries()->delete();
                            $voucher->delete();
                            $successCount++;
                        }
                        break;
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Bulk action completed successfully. {$successCount} vouchers processed.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to perform bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for voucher.
     */
    public function generatePdf(Tenant $tenant, Voucher $voucher)
    {
        $voucher->load(['voucherType', 'entries.ledgerAccount.accountGroup', 'createdBy', 'postedBy']);

        // You can use a PDF library like DomPDF or wkhtmltopdf
        // For now, returning a view that can be printed
        return view('tenant.accounting.vouchers.pdf', compact('tenant', 'voucher'));
    }

    /**
     * Generate PDF for voucher (route method).
     */
    public function pdf(Tenant $tenant, Voucher $voucher)
    {
        // Ensure the voucher belongs to the tenant
        if ($voucher->tenant_id !== $tenant->id) {
            abort(404);
        }

        $voucher->load(['voucherType', 'entries.ledgerAccount.accountGroup', 'createdBy', 'postedBy']);

        $pdf = Pdf::loadView('tenant.accounting.vouchers.pdf', compact('tenant', 'voucher'));

        return $pdf->stream($voucher->voucherType->name . '_' . $voucher->voucher_number . '.pdf');
    }

    /**
     * Print voucher.
     */
    public function print(Tenant $tenant, Voucher $voucher)
    {
        // Ensure the voucher belongs to the tenant
        if ($voucher->tenant_id !== $tenant->id) {
            abort(404);
        }

        $voucher->load(['voucherType', 'entries.ledgerAccount.accountGroup', 'createdBy', 'postedBy']);

        return view('tenant.accounting.vouchers.print', compact('tenant', 'voucher'));
    }

    /**
     * Show ledger statement for an account with date filtering
     * Similar to product stock movements, shows historical balances
     */
    public function ledgerStatement(Request $request, Tenant $tenant, LedgerAccount $ledgerAccount)
    {
        // Ensure account belongs to tenant
        if ($ledgerAccount->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Get date range (similar to product stock filtering)
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        // Calculate opening balance (balance as of day before from_date)
        $openingDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));
        $openingBalance = $ledgerAccount->getCurrentBalance($openingDate, false);

        // Get all entries for the period, ordered by voucher date
        $entries = $ledgerAccount->voucherEntries()
            ->with(['voucher.voucherType', 'voucher.createdBy'])
            ->whereHas('voucher', function ($query) use ($fromDate, $toDate) {
                $query->where('status', 'posted')
                      ->whereBetween('voucher_date', [$fromDate, $toDate]);
            })
            ->get()
            ->sortBy(function ($entry) {
                return $entry->voucher->voucher_date . ' ' . $entry->voucher->id;
            });

        // Build statement with running balance (like stock movements)
        $accountType = $ledgerAccount->account_type ?? 'asset';
        $runningBalance = $openingBalance;
        $statementLines = collect();

        foreach ($entries as $entry) {
            // Calculate movement based on account type
            if (in_array($accountType, ['asset', 'expense'])) {
                // Debit increases, Credit decreases
                $movement = $entry->debit_amount - $entry->credit_amount;
            } else {
                // Credit increases, Debit decreases (liability, equity, income)
                $movement = $entry->credit_amount - $entry->debit_amount;
            }

            $runningBalance += $movement;

            $statementLines->push([
                'id' => $entry->id,
                'date' => $entry->voucher->voucher_date,
                'voucher_number' => $entry->voucher->voucher_number,
                'voucher_type' => $entry->voucher->voucherType->name ?? 'Unknown',
                'particulars' => $entry->particulars,
                'reference' => $entry->voucher->reference_number,
                'debit_amount' => $entry->debit_amount,
                'credit_amount' => $entry->credit_amount,
                'movement' => $movement,
                'running_balance' => $runningBalance,
                'voucher_id' => $entry->voucher_id,
            ]);
        }

        // Calculate closing balance
        $closingBalance = $runningBalance;

        // Calculate period totals
        $periodDebits = $entries->sum('debit_amount');
        $periodCredits = $entries->sum('credit_amount');

        // Get current balance (as of today) for reference
        $currentBalance = $ledgerAccount->getCurrentBalance(null, false);

        return view('tenant.accounting.vouchers.ledger-statement', compact(
            'tenant',
            'ledgerAccount',
            'statementLines',
            'openingBalance',
            'closingBalance',
            'currentBalance',
            'periodDebits',
            'periodCredits',
            'fromDate',
            'toDate',
            'accountType'
        ));
    }
}
