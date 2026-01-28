<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\AccountGroup;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    /**
     * List salary advances and loans.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = EmployeeLoan::with(['employee.department', 'approvedBy'])
            ->whereHas('employee', function ($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->get('employee_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('loan_number', 'like', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 20);
        $loans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $loans->getCollection()->transform(function (EmployeeLoan $loan) {
            return $this->formatLoan($loan);
        });

        $summary = [
            'total_loans' => EmployeeLoan::whereHas('employee', fn ($q) => $q->where('tenant_id', $tenant->id))
                ->count(),
            'active_loans' => EmployeeLoan::whereHas('employee', fn ($q) => $q->where('tenant_id', $tenant->id))
                ->where('status', 'active')
                ->count(),
            'total_amount' => EmployeeLoan::whereHas('employee', fn ($q) => $q->where('tenant_id', $tenant->id))
                ->sum('loan_amount'),
            'total_outstanding' => EmployeeLoan::whereHas('employee', fn ($q) => $q->where('tenant_id', $tenant->id))
                ->sum('balance'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Loans retrieved successfully',
            'data' => [
                'summary' => $summary,
                'records' => $loans,
            ],
        ]);
    }

    /**
     * Show a loan record.
     */
    public function show(Tenant $tenant, EmployeeLoan $loan)
    {
        if ($loan->employee?->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Loan record not found',
            ], 404);
        }

        $loan->load(['employee.department', 'approvedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Loan record retrieved successfully',
            'data' => [
                'loan' => $this->formatLoan($loan),
            ],
        ]);
    }

    /**
     * Issue salary advance (creates loan + voucher).
     */
    public function storeSalaryAdvance(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'duration_months' => 'required|integer|min:1|max:12',
            'purpose' => 'nullable|string|max:500',
            'voucher_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank',
            'reference' => 'nullable|string|max:255',
            'cash_bank_account_id' => 'nullable|exists:ledger_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::where('id', $request->get('employee_id'))
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $monthlyDeduction = $request->get('amount') / $request->get('duration_months');

            $loan = EmployeeLoan::create([
                'employee_id' => $employee->id,
                'loan_amount' => $request->get('amount'),
                'monthly_deduction' => $monthlyDeduction,
                'duration_months' => $request->get('duration_months'),
                'start_date' => now(),
                'purpose' => $request->get('purpose') ?? 'Salary Advance',
                'status' => 'active',
                'approved_by' => Auth::id(),
            ]);

            $advanceAccount = LedgerAccount::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code' => '1130',
                ],
                [
                    'name' => 'Employee Advances',
                    'account_type' => 'asset',
                    'account_group_id' => AccountGroup::where('tenant_id', $tenant->id)
                        ->where('code', 'CA')
                        ->first()?->id,
                    'is_active' => true,
                ]
            );

            $cashOrBankAccount = null;
            if ($request->filled('cash_bank_account_id')) {
                $cashOrBankAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('id', $request->get('cash_bank_account_id'))
                    ->first();
            }

            if (!$cashOrBankAccount) {
                if ($request->get('payment_method') === 'cash') {
                    $cashOrBankAccount = LedgerAccount::where('tenant_id', $tenant->id)
                        ->where('name', 'Cash in Hand')
                        ->first();
                } else {
                    $cashOrBankAccount = LedgerAccount::where('tenant_id', $tenant->id)
                        ->where('account_type', 'asset')
                        ->where('name', 'like', '%Bank%')
                        ->first();
                }
            }

            if (!$cashOrBankAccount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cash/Bank account not found. Please configure ledger accounts.',
                ], 409);
            }

            $voucherType = VoucherType::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code' => 'SA',
                ],
                [
                    'name' => 'Salary Advance',
                    'description' => 'Salary advances to employees',
                    'current_number' => 0,
                    'prefix' => 'SA-',
                    'suffix' => '',
                    'is_active' => true,
                ]
            );

            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $voucherType->getNextVoucherNumber(),
                'voucher_date' => $request->get('voucher_date'),
                'reference_number' => $request->get('reference') ?? $loan->loan_number,
                'total_amount' => $request->get('amount'),
                'narration' => "Salary advance issued to {$employee->full_name} - {$loan->loan_number}",
                'status' => 'posted',
                'created_by' => Auth::id(),
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $advanceAccount->id,
                'debit_amount' => $request->get('amount'),
                'credit_amount' => 0,
                'particulars' => "Salary advance to {$employee->full_name} ({$employee->employee_number})",
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $cashOrBankAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $request->get('amount'),
                'particulars' => "Payment for salary advance - {$loan->loan_number}",
            ]);

            $voucherType->increment('current_number');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary advance issued successfully',
                'data' => [
                    'loan' => $this->formatLoan($loan->fresh(['employee.department', 'approvedBy'])),
                    'voucher' => [
                        'id' => $voucher->id,
                        'voucher_number' => $voucher->voucher_number,
                        'voucher_date' => Carbon::parse($voucher->voucher_date)->toDateString(),
                        'total_amount' => $voucher->total_amount,
                        'reference_number' => $voucher->reference_number,
                    ],
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to issue salary advance',
            ], 500);
        }
    }

    private function formatLoan(EmployeeLoan $loan): array
    {
        return [
            'id' => $loan->id,
            'loan_number' => $loan->loan_number,
            'employee_id' => $loan->employee_id,
            'employee_name' => $loan->employee?->full_name,
            'employee_number' => $loan->employee?->employee_number,
            'department_name' => $loan->employee?->department?->name,
            'loan_amount' => $loan->loan_amount,
            'total_paid' => $loan->total_paid,
            'balance' => $loan->balance,
            'monthly_deduction' => $loan->monthly_deduction,
            'duration_months' => $loan->duration_months,
            'remaining_months' => $loan->remaining_months,
            'progress_percentage' => $loan->progress_percentage,
            'status' => $loan->status,
            'purpose' => $loan->purpose,
            'start_date' => $loan->start_date?->toDateString(),
            'approved_by' => $loan->approvedBy?->name,
            'created_at' => $loan->created_at?->toDateTimeString(),
            'updated_at' => $loan->updated_at?->toDateTimeString(),
        ];
    }
}
