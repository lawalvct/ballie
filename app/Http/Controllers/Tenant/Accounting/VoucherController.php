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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaymentEntriesImport;
use App\Exports\PaymentEntriesTemplateExport;


use App\Services\VoucherTypeService;

class VoucherController extends Controller
{
    protected $voucherTypeService;

    public function __construct(VoucherTypeService $voucherTypeService)
    {
        $this->middleware(['auth', 'tenant']);
        $this->middleware('permission:accounting.vouchers.manage')
            ->except(['index', 'show', 'ledgerStatement']);
        $this->middleware('permission:accounting.view')
            ->only(['index', 'show', 'ledgerStatement']);
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

        $this->applyFilters($query, $request);
        $vouchers = $query->paginate(20)->appends($request->query());
        [$voucherTypes, $stats, $primaryVoucherTypes] = $this->getIndexData($tenant);

        return view('tenant.accounting.vouchers.index', compact(
            'tenant', 'vouchers', 'voucherTypes', 'stats', 'primaryVoucherTypes'
        ));
    }

    private function applyFilters($query, Request $request)
    {
        $query->when($request->filled('voucher_type'), fn($q) => $q->where('voucher_type_id', $request->voucher_type))
              ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
              ->when($request->filled('date_from'), fn($q) => $q->whereDate('voucher_date', '>=', $request->date_from))
              ->when($request->filled('date_to'), fn($q) => $q->whereDate('voucher_date', '<=', $request->date_to))
              ->when($request->filled('search'), function($q) use ($request) {
                  $search = $request->search;
                  $q->where(function ($query) use ($search) {
                      $query->where('voucher_number', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%")
                            ->orWhere('narration', 'like', "%{$search}%");
                  });
              });
    }

    private function getIndexData(Tenant $tenant)
    {
        $baseQuery = Voucher::where('tenant_id', $tenant->id);

        return [
            VoucherType::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get(),
            [
                'total_vouchers' => $baseQuery->count(),
                'draft_vouchers' => $baseQuery->where('status', 'draft')->count(),
                'posted_vouchers' => $baseQuery->where('status', 'posted')->count(),
                'total_amount' => $baseQuery->where('status', 'posted')->sum('total_amount'),
            ],
            VoucherType::where('tenant_id', $tenant->id)
                ->where('is_system_defined', true)
                ->orderByRaw("FIELD(code, 'JV', 'PV', 'RV', 'SV', 'PUR') DESC")
                ->orderBy('name')->get()
        ];
    }

    /**
     * Show the form for creating a new voucher.
     */
    public function create(Request $request, Tenant $tenant, $type = null)
    {
        $typeCode = $type ?? $request->get('type');
        $selectedType = $typeCode ? $this->getSelectedVoucherType($tenant, $typeCode) : null;

        $voucherTypes = VoucherType::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('affects_inventory', false)
            ->orderBy('name')
            ->get();

        $ledgerAccounts = LedgerAccount::with('accountGroup')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['primaryUnit', 'category'])
            ->orderBy('name')
            ->get();

        return view('tenant.accounting.vouchers.create', compact(
            'tenant', 'voucherTypes', 'ledgerAccounts', 'products', 'selectedType'
        ));
    }

    private function getSelectedVoucherType(Tenant $tenant, $typeCode)
    {
        $selectedType = VoucherType::where('tenant_id', $tenant->id)
            ->where('code', strtoupper($typeCode))
            ->first();

        if (!$selectedType) {
            abort(404, 'Invalid voucher type specified.');
        }

        return $selectedType;
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
            return back()->withErrors(['entries' => 'Voucher entries must be balanced.'])->withInput();
        }

        if ($totalDebits == 0) {
            return back()->withErrors(['entries' => 'Voucher must have valid entries.'])->withInput();
        }

        foreach ($request->entries as $index => $entry) {
            $debit = (float) ($entry['debit_amount'] ?? 0);
            $credit = (float) ($entry['credit_amount'] ?? 0);

            if ($debit > 0 && $credit > 0) {
                return back()->withErrors(["entries.{$index}" => 'Entry cannot have both debit and credit amounts.'])->withInput();
            }

            if ($debit == 0 && $credit == 0) {
                return back()->withErrors(["entries.{$index}" => 'Entry must have either debit or credit amount.'])->withInput();
            }
        }

        try {
            $voucher = $this->createVoucher($request, $tenant);
            return $this->handleVoucherAction($request, $tenant, $voucher);
        } catch (\Exception $e) {
            Log::error('Voucher creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create voucher.'])->withInput();
        }
    }

    private function createVoucher(Request $request, Tenant $tenant)
    {
        return DB::transaction(function () use ($request, $tenant) {
            $voucherType = VoucherType::findOrFail($request->voucher_type_id);

            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $request->voucher_type_id,
                'voucher_number' => $voucherType->getNextVoucherNumber(),
                'voucher_date' => $request->voucher_date,
                'reference_number' => $request->reference_number,
                'narration' => $request->narration,
                'total_amount' => collect($request->entries)->sum('debit_amount'),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->entries as $index => $entryData) {
                $debitAmount = (float) ($entryData['debit_amount'] ?? 0);
                $creditAmount = (float) ($entryData['credit_amount'] ?? 0);

                if ($debitAmount > 0 || $creditAmount > 0) {
                    $documentPath = null;
                    if ($request->hasFile("entries.{$index}.document")) {
                        $file = $request->file("entries.{$index}.document");
                        $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $documentPath = $file->storeAs('voucher_documents', $filename, 'public');
                    }

                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entryData['ledger_account_id'],
                        'particulars' => $entryData['particulars'],
                        'debit_amount' => $debitAmount,
                        'credit_amount' => $creditAmount,
                        'document_path' => $documentPath,
                    ]);
                }
            }

            return $voucher;
        });
    }

    private function handleVoucherAction(Request $request, Tenant $tenant, Voucher $voucher)
    {
        $action = $request->input('action');
        $voucherTypeName = $voucher->voucherType->name ?? 'Voucher';

        if (in_array($action, ['save_and_post', 'save_and_post_return'])) {
            $voucher->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            foreach ($voucher->entries as $entry) {
                $entry->updateLedgerAccountBalance();
            }

            if ($action === 'save_and_post_return') {
                $routeParams = ['tenant' => $tenant->slug];
                if ($typeCode = $voucher->voucherType->code ?? null) {
                    $routeParams['type'] = strtolower($typeCode);
                }

                return redirect()
                    ->route('tenant.accounting.vouchers.create', $routeParams)
                    ->with('success', $voucherTypeName . ' created and posted successfully. You can create another.');
            }

            return redirect()
                ->route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id])
                ->with('success', $voucherTypeName . ' created and posted successfully.');
        }

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

    /**
     * Show simple payment recording form
     */
    public function recordPayment(Tenant $tenant)
    {
        $customers = \App\Models\Customer::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('ledgerAccount')
            ->orderBy('first_name')
            ->get();

        $vendors = \App\Models\Vendor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('ledgerAccount')
            ->orderBy('first_name')
            ->get();

        $bankAccounts = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'like', '%bank%')
                  ->orWhere('name', 'like', '%cash%');
            })
            ->orderBy('name')
            ->get();

        return view('tenant.crm.record-payment', compact('tenant', 'customers', 'vendors', 'bankAccounts'));
    }

    /**
     * Store payment from simple form
     */
    public function storePayment(Request $request, Tenant $tenant)
    {
        $request->validate([
            'party_type' => 'required|in:customer,vendor',
            'party_ledger_id' => 'required|exists:ledger_accounts,id',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'bank_account_id' => 'required|exists:ledger_accounts,id',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $voucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where('code', 'RV')
            ->first();

        if (!$voucherType) {
            return back()->withErrors(['error' => 'Receipt voucher type not found.'])->withInput();
        }

        $entries = [
            [
                'ledger_account_id' => $request->party_ledger_id,
                'particulars' => $request->notes ?? 'Payment received',
                'debit_amount' => 0,
                'credit_amount' => $request->amount,
            ],
            [
                'ledger_account_id' => $request->bank_account_id,
                'particulars' => $request->notes ?? 'Payment received',
                'debit_amount' => $request->amount,
                'credit_amount' => 0,
            ]
        ];

        $request->merge([
            'voucher_type_id' => $voucherType->id,
            'voucher_date' => $request->receipt_date,
            'narration' => $request->notes,
            'entries' => $entries,
        ]);

        try {
            $voucher = $this->createVoucher($request, $tenant);
            
            $voucher->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            foreach ($voucher->entries as $entry) {
                $entry->updateLedgerAccountBalance();
            }

            return redirect()
                ->route('tenant.crm.record-payment', $tenant->slug)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Payment recording failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to record payment.'])->withInput();
        }
    }

    /**
     * Download bulk payment entries template
     */
    public function downloadBulkPaymentTemplate(Tenant $tenant)
    {
        $fileName = 'bulk_payment_entries_template_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new PaymentEntriesTemplateExport($tenant->id), $fileName);
    }

    /**
     * Upload and process bulk payment entries
     */
    public function uploadBulkPayments(Request $request, Tenant $tenant)
    {
        // Validate request
        $request->validate([
            'bank_account_id' => 'required|exists:ledger_accounts,id',
            'voucher_date' => 'required|date',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'narration' => 'nullable|string|max:500',
        ]);

        try {
            // Verify bank account belongs to tenant
            $bankAccount = LedgerAccount::where('id', $request->bank_account_id)
                ->where('tenant_id', $tenant->id)
                ->firstOrFail();

            // Process the import file
            $import = new PaymentEntriesImport($tenant->id);
            Excel::import($import, $request->file('file'));

            // Check for validation errors
            if ($import->hasErrors()) {
                return response()->json([
                    'success' => false,
                    'errors' => $import->getErrors(),
                    'message' => 'Validation failed for ' . count($import->getErrors()) . ' row(s). Please fix the errors and try again.'
                ], 422);
            }

            // Get validated entries
            $entries = $import->getEntries();

            if (empty($entries)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid entries found in the uploaded file.'
                ], 422);
            }

            // Get payment voucher type
            $voucherType = VoucherType::where('name', 'Payment')
                ->where('tenant_id', $tenant->id)
                ->first();

            if (!$voucherType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment voucher type not found for this tenant.'
                ], 422);
            }

            // Generate bulk upload reference
            $bulkUploadReference = 'BULK_' . strtoupper(Str::random(10)) . '_' . time();
            $uploadedFileName = $request->file('file')->getClientOriginalName();

            // Save voucher with all entries in a transaction
            DB::beginTransaction();

            try {
                // Generate voucher number
                $voucherNumber = $voucherType->getNextVoucherNumber();

                // Calculate total amount before creating voucher
                $totalDebitAmount = $import->getTotalAmount();

                // Create the voucher
                $voucher = Voucher::create([
                    'tenant_id' => $tenant->id,
                    'voucher_type_id' => $voucherType->id,
                    'voucher_number' => $voucherNumber,
                    'voucher_date' => $request->voucher_date,
                    'reference_number' => $bulkUploadReference,
                    'narration' => $request->narration ?? 'Bulk payment upload from ' . $uploadedFileName,
                    'total_amount' => $totalDebitAmount,
                    'status' => 'draft',
                    'created_by' => Auth::id(),
                    'bulk_upload_reference' => $bulkUploadReference,
                    'uploaded_file_name' => $uploadedFileName,
                ]);

                // Create credit entry for bank account
                VoucherEntry::create([
                    'voucher_id' => $voucher->id,
                    'ledger_account_id' => $bankAccount->id,
                    'particulars' => 'Bulk payment - ' . count($entries) . ' entries',
                    'debit_amount' => 0,
                    'credit_amount' => $totalDebitAmount,
                ]);

                // Create debit entries for each expense account
                foreach ($entries as $entry) {
                    VoucherEntry::create([
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $entry['ledger_account_id'],
                        'particulars' => $entry['particulars'],
                        'debit_amount' => $entry['debit_amount'],
                        'credit_amount' => 0,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully uploaded ' . count($entries) . ' payment entries.',
                    'voucher_id' => $voucher->id,
                    'voucher_number' => $voucherNumber,
                    'redirect_url' => route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id])
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Bulk payment upload database error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save voucher: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Bulk payment upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the file: ' . $e->getMessage()
            ], 500);
        }
    }
}
