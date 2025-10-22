# Date-Based Ledger Balance - Quick Implementation Guide

## ✅ Completed Backend Changes

### 1. LedgerAccountController@show (Updated)

-   Added date range parameters: `from_date`, `to_date`, `as_of_date`
-   Calculates opening balance (day before from_date)
-   Filters transactions by voucher_date
-   Calculates closing balance and period movement
-   Returns current balance for comparison

### 2. VoucherController@ledgerStatement (New Method)

-   Generates detailed ledger statement with running balances
-   Similar to product stock movements
-   Respects account type for balance calculation
-   Shows complete transaction history for period

## 🔧 Required Next Steps

### Step 1: Add Route

**File:** `routes/tenant.php`

Add after other voucher routes:

```php
// Ledger Statement
Route::get('/accounting/vouchers/ledger-statement/{ledgerAccount}',
    [VoucherController::class, 'ledgerStatement'])
    ->name('accounting.vouchers.ledger-statement');
```

### Step 2: Update Ledger Account Show View

**File:** `resources/views/tenant/accounting/ledger-accounts/show.blade.php`

Add date filter form at the top:

```html
<!-- Date Range Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h3 class="text-lg font-semibold mb-4">Filter by Date Range</h3>
    <form
        method="GET"
        action="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
        class="flex gap-4"
    >
        <div>
            <label class="block text-sm font-medium mb-1">From Date</label>
            <input
                type="date"
                name="from_date"
                value="{{ $fromDate }}"
                class="border rounded px-3 py-2"
            />
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">To Date</label>
            <input
                type="date"
                name="to_date"
                value="{{ $toDate }}"
                class="border rounded px-3 py-2"
            />
        </div>
        <div class="flex items-end">
            <button
                type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600"
            >
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Balance Summary -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <h4 class="text-sm text-gray-600">Opening Balance</h4>
        <p
            class="text-2xl font-bold {{ $openingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}"
        >
            ₦{{ number_format(abs($openingBalance), 2) }}
            <span class="text-sm"
                >{{ $openingBalance >= 0 ? 'Dr' : 'Cr' }}</span
            >
        </p>
        <p class="text-xs text-gray-500">
            as of {{ date('d M Y', strtotime($fromDate . ' -1 day')) }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h4 class="text-sm text-gray-600">Period Debits</h4>
        <p class="text-2xl font-bold text-blue-600">
            ₦{{ number_format($totalDebits, 2) }}
        </p>
        <p class="text-xs text-gray-500">Total</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h4 class="text-sm text-gray-600">Period Credits</h4>
        <p class="text-2xl font-bold text-purple-600">
            ₦{{ number_format($totalCredits, 2) }}
        </p>
        <p class="text-xs text-gray-500">Total</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h4 class="text-sm text-gray-600">Closing Balance</h4>
        <p
            class="text-2xl font-bold {{ $closingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}"
        >
            ₦{{ number_format(abs($closingBalance), 2) }}
            <span class="text-sm"
                >{{ $closingBalance >= 0 ? 'Dr' : 'Cr' }}</span
            >
        </p>
        <p class="text-xs text-gray-500">
            as of {{ date('d M Y', strtotime($toDate)) }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-4 border-2 border-blue-300">
        <h4 class="text-sm text-gray-600">Current Balance</h4>
        <p
            class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-green-600' : 'text-red-600' }}"
        >
            ₦{{ number_format(abs($currentBalance), 2) }}
            <span class="text-sm"
                >{{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}</span
            >
        </p>
        <p class="text-xs text-gray-500">as of {{ date('d M Y') }}</p>
    </div>
</div>

<!-- Add link to detailed statement -->
<div class="mb-4">
    <a
        href="{{ route('accounting.vouchers.ledger-statement', [$tenant, $ledgerAccount]) }}?from_date={{ $fromDate }}&to_date={{ $toDate }}"
        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
    >
        <svg
            class="w-5 h-5 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            ></path>
        </svg>
        View Detailed Statement
    </a>
</div>
```

### Step 3: Create Ledger Statement View

**File:** `resources/views/tenant/accounting/vouchers/ledger-statement.blade.php`

```html
@extends('layouts.tenant') @section('title', 'Ledger Statement - ' .
$ledgerAccount->name) @section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Ledger Statement</h1>
            <p class="text-gray-600">
                {{ $ledgerAccount->name }} ({{ $ledgerAccount->code }})
            </p>
        </div>
        <a
            href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
        >
            Back to Account
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium mb-1">From Date</label>
                <input
                    type="date"
                    name="from_date"
                    value="{{ $fromDate }}"
                    class="border rounded px-3 py-2"
                />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">To Date</label>
                <input
                    type="date"
                    name="to_date"
                    value="{{ $toDate }}"
                    class="border rounded px-3 py-2"
                />
            </div>
            <button
                type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600"
            >
                Filter
            </button>
            <button
                type="button"
                onclick="window.print()"
                class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700"
            >
                Print
            </button>
        </form>
    </div>

    <!-- Statement Summary -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                        Account Details
                    </th>
                    <th
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                    >
                        Amount
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 text-sm font-medium">
                        Opening Balance
                    </td>
                    <td
                        class="px-6 py-4 text-sm text-right font-bold {{ $openingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}"
                    >
                        ₦{{ number_format(abs($openingBalance), 2) }} {{
                        $openingBalance >= 0 ? 'Dr' : 'Cr' }}
                    </td>
                </tr>
                <tr class="bg-blue-50">
                    <td class="px-6 py-4 text-sm">Period Debits</td>
                    <td class="px-6 py-4 text-sm text-right text-blue-600">
                        ₦{{ number_format($periodDebits, 2) }}
                    </td>
                </tr>
                <tr class="bg-purple-50">
                    <td class="px-6 py-4 text-sm">Period Credits</td>
                    <td class="px-6 py-4 text-sm text-right text-purple-600">
                        ₦{{ number_format($periodCredits, 2) }}
                    </td>
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium">
                        Closing Balance
                    </td>
                    <td
                        class="px-6 py-4 text-sm text-right font-bold {{ $closingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}"
                    >
                        ₦{{ number_format(abs($closingBalance), 2) }} {{
                        $closingBalance >= 0 ? 'Dr' : 'Cr' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Transaction Details -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-100 border-b">
            <h2 class="text-lg font-semibold">Transaction Details</h2>
            <p class="text-sm text-gray-600">
                Period: {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d
                M Y', strtotime($toDate)) }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Date
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Voucher
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Particulars
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                        >
                            Debit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                        >
                            Credit
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                        >
                            Balance
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($statementLines as $line)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            {{ date('d-m-Y', strtotime($line['date'])) }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a
                                href="{{ route('tenant.accounting.vouchers.show', [$tenant, $line['voucher_id']]) }}"
                                class="text-blue-600 hover:underline"
                            >
                                {{ $line['voucher_type'] }}-{{
                                $line['voucher_number'] }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $line['particulars'] }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            {{ $line['debit_amount'] > 0 ? '₦' .
                            number_format($line['debit_amount'], 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            {{ $line['credit_amount'] > 0 ? '₦' .
                            number_format($line['credit_amount'], 2) : '-' }}
                        </td>
                        <td
                            class="px-4 py-3 text-sm text-right font-medium {{ $line['running_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}"
                        >
                            ₦{{ number_format(abs($line['running_balance']), 2)
                            }} {{ $line['running_balance'] >= 0 ? 'Dr' : 'Cr' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="6"
                            class="px-4 py-8 text-center text-gray-500"
                        >
                            No transactions found for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            font-size: 12px;
        }
    }
</style>
@endsection
```

## 🧪 Testing Guide

### Test Scenario 1: Expense Account

1. **Navigate to Ledger Account:** Go to an expense account (e.g., Office Expenses)

2. **Filter by Date:** Set from_date to start of month, to_date to 10th

3. **Verify:**
    - Opening balance shows balance before start of month
    - Only transactions between dates are shown
    - Closing balance = Opening + Period Debits - Period Credits
    - Current balance shows today's balance (likely different)

### Test Scenario 2: Revenue Account

1. **Navigate to Ledger Account:** Go to a revenue account (e.g., Sales Revenue)

2. **Filter by Date:** Set from_date to 1st October, to_date to 15th October

3. **Verify:**
    - Opening balance shows balance as of 30th September
    - Period credits increase the balance
    - Closing balance = Opening + Period Credits - Period Debits
    - Current balance reflects all transactions to date

### Test Scenario 3: Ledger Statement

1. **Click "View Detailed Statement"** from ledger account page

2. **Verify:**
    - Each transaction shows running balance
    - Running balances calculate correctly based on account type
    - Can filter by different date ranges
    - Print-friendly format

## 📊 Expected Behavior

### Expense Account (Asset/Expense Type)

```
Opening: ₦500,000 Dr
Debit:   ₦100,000 (increases balance)
Credit:  ₦20,000  (decreases balance)
Closing: ₦580,000 Dr
```

### Revenue Account (Income Type)

```
Opening: ₦2,000,000 Cr
Credit:  ₦500,000 (increases balance)
Debit:   ₦50,000  (decreases balance)
Closing: ₦2,450,000 Cr
```

## ✨ Key Features

1. **Date Range Filtering** - View any historical period
2. **Opening Balance** - Shows balance before period starts
3. **Period Totals** - Debits and Credits for the period
4. **Closing Balance** - Balance at end of selected period
5. **Current Balance** - Today's balance for comparison
6. **Running Balances** - Transaction-by-transaction balance tracking
7. **Account Type Aware** - Respects asset/liability/income/expense logic
8. **Print Support** - Clean printable format

## 🔍 Troubleshooting

**Issue:** Balances don't match expectations

**Solution:**

-   Verify account_type is set correctly
-   Check that vouchers are posted (not draft)
-   Ensure voucher_date is used (not created_at)

**Issue:** No transactions showing

**Solution:**

-   Check date range includes transactions
-   Verify vouchers are posted
-   Confirm account has activity in that period
