<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Models\Tenant;
use App\Models\VoucherEntry;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CrmReportsController extends Controller
{
    /**
     * Customer activities report.
     */
    public function activities(Request $request, Tenant $tenant)
    {
        $query = CustomerActivity::where('tenant_id', $tenant->id)
            ->with(['customer', 'user']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->get('activity_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->get('date_to'));
        }

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->get('search') . '%');
        }

        $perPage = (int) $request->get('per_page', 20);
        $activities = $query->orderBy('activity_date', 'desc')->paginate($perPage);

        $activities->getCollection()->transform(function ($activity) {
            return [
                'id' => $activity->id,
                'customer' => [
                    'id' => $activity->customer?->id,
                    'name' => $activity->customer?->full_name ?? $activity->customer?->company_name,
                ],
                'activity_type' => $activity->activity_type,
                'subject' => $activity->subject,
                'description' => $activity->description,
                'activity_date' => $activity->activity_date,
                'status' => $activity->status,
                'user' => [
                    'id' => $activity->user?->id,
                    'name' => $activity->user?->name,
                ],
                'created_at' => $activity->created_at,
                'updated_at' => $activity->updated_at,
            ];
        });

        $customers = Customer::where('tenant_id', $tenant->id)
            ->select('id', 'first_name', 'last_name', 'company_name', 'customer_type')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->full_name ?? $customer->company_name,
                    'customer_type' => $customer->customer_type,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Customer activities retrieved successfully',
            'data' => [
                'filters' => [
                    'customer_id' => $request->get('customer_id'),
                    'activity_type' => $request->get('activity_type'),
                    'status' => $request->get('status'),
                    'date_from' => $request->get('date_from'),
                    'date_to' => $request->get('date_to'),
                    'search' => $request->get('search'),
                ],
                'records' => $activities,
                'customers' => $customers,
            ],
        ]);
    }

    /**
     * Customer statements report.
     */
    public function customerStatements(Request $request, Tenant $tenant)
    {
        $query = Customer::with(['invoices', 'payments', 'ledgerAccount'])
            ->where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->get('customer_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $customers = $query->get()->map(function ($customer) {
            $ledgerAccount = $customer->ledgerAccount;

            if ($ledgerAccount) {
                $currentBalance = $ledgerAccount->getCurrentBalance();
                $totalDebits = $ledgerAccount->getTotalDebits();
                $totalCredits = $ledgerAccount->getTotalCredits();
                $balanceType = $ledgerAccount->getBalanceType($currentBalance);

                $customer->total_debits = $totalDebits;
                $customer->total_credits = $totalCredits;
                $customer->current_balance = abs($currentBalance);
                $customer->balance_type = $balanceType;
                $customer->running_balance = $currentBalance;
            } else {
                $customer->total_debits = 0;
                $customer->total_credits = 0;
                $customer->current_balance = 0;
                $customer->balance_type = 'dr';
                $customer->running_balance = 0;
            }

            return $customer;
        });

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'current_balance') {
            $customers = $sortDirection === 'desc'
                ? $customers->sortByDesc('current_balance')
                : $customers->sortBy('current_balance');
        } elseif ($sortField === 'total_debits') {
            $customers = $sortDirection === 'desc'
                ? $customers->sortByDesc('total_debits')
                : $customers->sortBy('total_debits');
        } elseif ($sortField === 'total_credits') {
            $customers = $sortDirection === 'desc'
                ? $customers->sortByDesc('total_credits')
                : $customers->sortBy('total_credits');
        }

        $totalCustomers = $customers->count();
        $totalReceivable = $customers->where('running_balance', '>', 0)->sum('running_balance');
        $totalPayable = abs($customers->where('running_balance', '<', 0)->sum('running_balance'));
        $netBalance = $customers->sum('running_balance');

        $perPage = (int) $request->get('per_page', 50);
        $currentPage = (int) $request->get('page', 1);
        $items = $customers->forPage($currentPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $customers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $paginated->getCollection()->transform(function ($customer) {
            return [
                'id' => $customer->id,
                'customer_code' => $customer->customer_code,
                'name' => $customer->full_name ?? $customer->company_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'customer_type' => $customer->customer_type,
                'status' => $customer->status,
                'total_debits' => (float) $customer->total_debits,
                'total_credits' => (float) $customer->total_credits,
                'running_balance' => (float) $customer->running_balance,
                'current_balance' => (float) $customer->current_balance,
                'balance_type' => $customer->balance_type,
                'last_activity_at' => $customer->updated_at,
                'ledger_account_id' => $customer->ledgerAccount?->id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Customer statements retrieved successfully',
            'data' => [
                'filters' => [
                    'search' => $request->get('search'),
                    'customer_type' => $request->get('customer_type'),
                    'status' => $request->get('status'),
                    'sort' => $sortField,
                    'direction' => $sortDirection,
                ],
                'summary' => [
                    'total_customers' => $totalCustomers,
                    'total_receivable' => (float) $totalReceivable,
                    'total_payable' => (float) $totalPayable,
                    'net_balance' => (float) $netBalance,
                ],
                'records' => $paginated,
            ],
        ]);
    }

    /**
     * Payment reports.
     */
    public function paymentReports(Request $request, Tenant $tenant)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $payments = VoucherEntry::whereHas('voucher', function ($q) use ($tenant, $startDate, $endDate) {
                $q->where('tenant_id', $tenant->id)
                    ->where('status', 'posted')
                    ->whereHas('voucherType', function ($vt) {
                        $vt->where('code', 'RV');
                    })
                    ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->where('credit_amount', '>', 0)
            ->with(['voucher', 'ledgerAccount'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPayments = $payments->sum('credit_amount');
        $paymentCount = $payments->count();

        $records = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'voucher_id' => $payment->voucher?->id,
                'voucher_number' => $payment->voucher?->voucher_number,
                'voucher_date' => $payment->voucher?->voucher_date,
                'ledger_account_id' => $payment->ledgerAccount?->id,
                'ledger_account_name' => $payment->ledgerAccount?->name,
                'amount' => (float) $payment->credit_amount,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment reports retrieved successfully',
            'data' => [
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'summary' => [
                    'total_payments' => (float) $totalPayments,
                    'payment_count' => $paymentCount,
                ],
                'records' => $records,
            ],
        ]);
    }
}
