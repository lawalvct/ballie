<?php

namespace App\Http\Controllers\Api\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\AccountGroup;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class VendorController extends Controller
{
    /**
     * List vendors with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = Vendor::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('vendor_type')) {
            $query->where('vendor_type', $request->get('vendor_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['first_name', 'last_name', 'company_name', 'email', 'created_at', 'status'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $perPage = (int) $request->get('per_page', 15);
        $vendors = $query->paginate($perPage);

        $vendors->getCollection()->transform(function ($vendor) {
            $vendor->display_name = $vendor->company_name
                ?? trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? ''))
                ?? $vendor->email;
            $vendor->outstanding_balance = $vendor->ledgerAccount?->getCurrentBalance()
                ?? $vendor->ledgerAccount?->current_balance
                ?? 0;

            return $vendor;
        });

        $statistics = [
            'total_vendors' => Vendor::where('tenant_id', $tenant->id)->count(),
            'active_vendors' => Vendor::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'total_purchases' => Vendor::where('tenant_id', $tenant->id)->sum('total_purchases'),
            'total_outstanding' => Vendor::where('tenant_id', $tenant->id)->sum('outstanding_balance'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully',
            'data' => $vendors,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show vendor details.
     */
    public function show(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->load('ledgerAccount');
        $outstandingBalance = $vendor->ledgerAccount?->getCurrentBalance()
            ?? $vendor->ledgerAccount?->current_balance
            ?? 0;

        return response()->json([
            'success' => true,
            'message' => 'Vendor retrieved successfully',
            'data' => [
                'vendor' => $vendor,
                'outstanding_balance' => $outstandingBalance,
            ],
        ]);
    }

    /**
     * Vendor statements list with balances.
     */
    public function statements(Request $request, Tenant $tenant)
    {
        $query = Vendor::where('tenant_id', $tenant->id)
            ->with('ledgerAccount');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('vendor_type')) {
            $query->where('vendor_type', $request->get('vendor_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $vendors = $query->get()->map(function ($vendor) {
            $balance = $vendor->ledgerAccount?->getCurrentBalance()
                ?? $vendor->ledgerAccount?->current_balance
                ?? 0;
            $vendor->running_balance = $balance;
            $vendor->balance_type = $balance >= 0 ? 'payable' : 'receivable';
            $vendor->display_name = $vendor->display_name
                ?? $vendor->full_name
                ?? trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? ''))
                ?? $vendor->company_name
                ?? $vendor->email;

            return $vendor;
        });

        $totalVendors = $vendors->count();
        $totalPayable = $vendors->where('running_balance', '>', 0)->sum('running_balance');
        $totalReceivable = abs($vendors->where('running_balance', '<', 0)->sum('running_balance'));
        $netBalance = $vendors->sum('running_balance');

        $perPage = (int) $request->get('per_page', 50);
        $currentPage = (int) $request->get('page', 1);
        $items = $vendors->forPage($currentPage, $perPage)->values();

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $totalVendors,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Vendor statements retrieved successfully',
            'data' => $paginated,
            'statistics' => [
                'total_vendors' => $totalVendors,
                'total_payable' => $totalPayable,
                'total_receivable' => $totalReceivable,
                'net_balance' => $netBalance,
            ],
        ]);
    }

    /**
     * Vendor statement detail.
     */
    public function statement(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->load('ledgerAccount');
        if (!$vendor->ledgerAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor ledger account not found',
            ], 422);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $statementData = $this->buildStatementData($tenant, $vendor, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'message' => 'Vendor statement retrieved successfully',
            'data' => [
                'vendor' => $statementData['vendor'],
                'period' => $statementData['period'],
                'opening_balance' => $statementData['opening_balance'],
                'total_debits' => $statementData['total_debits'],
                'total_credits' => $statementData['total_credits'],
                'closing_balance' => $statementData['closing_balance'],
                'transactions' => $statementData['transactions'],
            ],
        ]);
    }

    /**
     * Download vendor statement as PDF.
     */
    public function statementPdf(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if (!auth()->check() && !$this->authenticateFromToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->load('ledgerAccount');
        if (!$vendor->ledgerAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor ledger account not found',
            ], 422);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $statementData = $this->buildStatementData($tenant, $vendor, $startDate, $endDate);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.crm.customers.statement-pdf',
            [
                'tenant' => $tenant,
                'customer' => $statementData['vendor'],
                'ledgerAccount' => $statementData['ledger_account'],
                'period' => $statementData['period'],
                'openingBalance' => $statementData['opening_balance'],
                'totalDebits' => $statementData['total_debits'],
                'totalCredits' => $statementData['total_credits'],
                'closingBalance' => $statementData['closing_balance'],
                'transactions' => $statementData['transactions'],
            ]
        );

        $filename = 'vendor-statement-' . $vendor->id . '-' . $startDate . '-to-' . $endDate . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download vendor statement as Excel (CSV).
     */
    public function statementExcel(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if (!auth()->check() && !$this->authenticateFromToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->load('ledgerAccount');
        if (!$vendor->ledgerAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor ledger account not found',
            ], 422);
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $statementData = $this->buildStatementData($tenant, $vendor, $startDate, $endDate);

        $filename = 'vendor-statement-' . $vendor->id . '-' . $startDate . '-to-' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($statementData) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Vendor Statement']);
            fputcsv($handle, ['Vendor', $statementData['vendor']->display_name ?? $statementData['vendor']->company_name ?? $statementData['vendor']->email]);
            fputcsv($handle, ['Period', $statementData['period']['start_date'] . ' to ' . $statementData['period']['end_date']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Opening Balance', $statementData['opening_balance']]);
            fputcsv($handle, ['Total Debits', $statementData['total_debits']]);
            fputcsv($handle, ['Total Credits', $statementData['total_credits']]);
            fputcsv($handle, ['Closing Balance', $statementData['closing_balance']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Date', 'Particulars', 'Voucher Type', 'Voucher Number', 'Debit', 'Credit', 'Running Balance']);

            foreach ($statementData['transactions'] as $row) {
                fputcsv($handle, [
                    $row['date'],
                    $row['particulars'],
                    $row['voucher_type'],
                    $row['voucher_number'],
                    $row['debit'],
                    $row['credit'],
                    $row['running_balance'],
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build vendor statement data (used by API + exports).
     */
    private function buildStatementData(Tenant $tenant, Vendor $vendor, string $startDate, string $endDate): array
    {
        $ledgerAccount = $vendor->ledgerAccount;

        $openingBalance = VoucherEntry::where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function ($q) use ($tenant, $startDate) {
                $q->where('tenant_id', $tenant->id)
                    ->where('status', Voucher::STATUS_POSTED)
                    ->where('voucher_date', '<', $startDate);
            })
            ->selectRaw('SUM(debit_amount) as total_debits, SUM(credit_amount) as total_credits')
            ->first();

        $openingBalanceAmount = ($openingBalance->total_debits ?? 0) - ($openingBalance->total_credits ?? 0);

        $transactions = VoucherEntry::with(['voucher.voucherType'])
            ->where('ledger_account_id', $ledgerAccount->id)
            ->whereHas('voucher', function ($q) use ($tenant, $startDate, $endDate, $ledgerAccount) {
                $q->where('tenant_id', $tenant->id)
                    ->where('status', Voucher::STATUS_POSTED)
                    ->where('id', '!=', $ledgerAccount->opening_balance_voucher_id)
                    ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->orderBy('id')
            ->get();

        $runningBalance = $openingBalanceAmount;
        $transactionsWithBalance = [];

        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction->debit_amount - $transaction->credit_amount);
            $transactionsWithBalance[] = [
                'date' => $transaction->voucher->voucher_date->format('Y-m-d'),
                'particulars' => $transaction->particulars ?? ($transaction->voucher->voucherType->name ?? null),
                'voucher_type' => $transaction->voucher->voucherType->name ?? null,
                'voucher_number' => ($transaction->voucher->voucherType->prefix ?? '') . $transaction->voucher->voucher_number,
                'debit' => (float) $transaction->debit_amount,
                'credit' => (float) $transaction->credit_amount,
                'running_balance' => (float) $runningBalance,
            ];
        }

        $totalDebits = collect($transactionsWithBalance)->sum('debit');
        $totalCredits = collect($transactionsWithBalance)->sum('credit');
        $closingBalance = $runningBalance;

        return [
            'vendor' => $vendor,
            'ledger_account' => $ledgerAccount,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'opening_balance' => $openingBalanceAmount,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'closing_balance' => $closingBalance,
            'transactions' => $transactionsWithBalance,
        ];
    }

    /**
     * Authenticate using token passed in query for download links.
     */
    private function authenticateFromToken(Request $request): bool
    {
        $tokenValue = $request->query('access_token') ?? $request->query('token');
        if (!$tokenValue) {
            return false;
        }

        $token = PersonalAccessToken::findToken($tokenValue);
        if (!$token || !$token->tokenable) {
            return false;
        }

        auth()->setUser($token->tokenable);

        return true;
    }

    /**
     * Create a new vendor.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:vendors,email,NULL,id,tenant_id,' . $tenant->id,
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
            'opening_balance_amount' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'nullable|in:none,debit,credit',
            'opening_balance_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $vendor = new Vendor($request->except([
                'opening_balance_amount',
                'opening_balance_type',
                'opening_balance_date',
            ]));
            $vendor->tenant_id = $tenant->id;
            $vendor->status = $vendor->status ?? 'active';
            $vendor->save();

            $vendor->refresh();

            if (!$vendor->ledgerAccount) {
                $vendor->createLedgerAccount();
                $vendor->refresh();
            }

            $openingBalanceAmount = (float) $request->input('opening_balance_amount', 0);
            $openingBalanceType = $request->input('opening_balance_type', 'none');
            $openingBalanceDate = $request->input('opening_balance_date', now()->format('Y-m-d'));

            if ($openingBalanceAmount > 0 && $openingBalanceType !== 'none') {
                $this->createOpeningBalanceVoucher($vendor, $openingBalanceAmount, $openingBalanceType, $openingBalanceDate);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully',
                'data' => $vendor->load('ledgerAccount'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create vendor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update vendor details.
     */
    public function update(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:vendors,email,' . $vendor->id . ',id,tenant_id,' . $tenant->id,
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $vendor->fill($request->all());
        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor updated successfully',
            'data' => $vendor->load('ledgerAccount'),
        ]);
    }

    /**
     * Delete a vendor.
     */
    public function destroy(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        if ($vendor->outstanding_balance > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor cannot be deleted because it has outstanding balance',
            ], 422);
        }

        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully',
        ]);
    }

    /**
     * Toggle vendor status.
     */
    public function toggleStatus(Request $request, Tenant $tenant, Vendor $vendor)
    {
        if ($vendor->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $vendor->status = $vendor->status === 'active' ? 'inactive' : 'active';
        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor status updated successfully',
            'data' => [
                'id' => $vendor->id,
                'status' => $vendor->status,
            ],
        ]);
    }

    /**
     * Create opening balance voucher for vendor
     */
    private function createOpeningBalanceVoucher(Vendor $vendor, $amount, $type, $date)
    {
        $journalVoucherType = VoucherType::where('tenant_id', $vendor->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        $openingBalanceEquity = LedgerAccount::where('tenant_id', $vendor->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            $equityGroup = AccountGroup::where('tenant_id', $vendor->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $vendor->tenant_id,
                    'name' => 'Equity',
                    'nature' => 'equity',
                    'code' => 'EQ',
                    'description' => 'Equity accounts',
                    'parent_id' => null,
                    'is_active' => true,
                ]);
            }

            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $vendor->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            $openingBalanceEquity = LedgerAccount::create([
                'tenant_id' => $vendor->tenant_id,
                'name' => 'Opening Balance Equity',
                'code' => $code,
                'account_group_id' => $equityGroup->id,
                'description' => 'Opening balance equity account',
                'opening_balance' => 0,
                'current_balance' => 0,
                'nature' => 'equity',
                'is_opening_balance_account' => true,
                'is_active' => true,
            ]);
        }

        $vendorName = $vendor->company_name ?: trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? ''));

        $voucher = Voucher::create([
            'tenant_id' => $vendor->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $vendorName,
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => auth()->id(),
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        if ($type === 'credit') {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $vendor->ledgerAccount->id,
                'credit_amount' => $amount,
                'debit_amount' => 0,
                'narration' => 'Opening Balance - Vendor Payable',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        } else {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $vendor->ledgerAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance - Vendor Advance',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'credit_amount' => $amount,
                'debit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        }

        $vendor->ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
            'opening_balance' => $type === 'credit' ? $amount : -$amount,
        ]);

        if (method_exists($vendor->ledgerAccount, 'updateCurrentBalance')) {
            $vendor->ledgerAccount->updateCurrentBalance();
        }

        return $voucher;
    }
}
