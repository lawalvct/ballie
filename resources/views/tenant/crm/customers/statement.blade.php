@extends('layouts.tenant')

@section('title', 'Customer Statement - ' . ($customer->customer_type === 'individual' ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Customer Statement</h1>
                <p class="text-gray-600 mt-1">
                    {{ $customer->customer_type === 'individual' ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.crm.customers.show', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Customer
                </a>
                <button onclick="window.print()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button onclick="downloadPDF()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i> Download PDF
                </button>
            </div>
        </div>

        <!-- Customer Info & Date Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Customer Code:</span>
                                <span class="font-semibold">{{ $customer->customer_code }}</span>
                            </div>
                            @if($customer->email)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-semibold">{{ $customer->email }}</span>
                            </div>
                            @endif
                            @if($customer->phone)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-semibold">{{ $customer->phone }}</span>
                            </div>
                            @endif
                            @if($customer->address)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Address:</span>
                                <span class="font-semibold text-right">{{ $customer->address }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statement Period</h3>
                        <form method="GET" action="{{ route('tenant.crm.customers.statement', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}" class="space-y-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="date"
                                       name="start_date"
                                       id="start_date"
                                       value="{{ $startDate }}"
                                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date"
                                       name="end_date"
                                       id="end_date"
                                       value="{{ $endDate }}"
                                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                <i class="fas fa-filter mr-2"></i> Apply Filter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statement Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="text-sm text-blue-600 font-medium mb-1">Opening Balance</div>
                <div class="text-2xl font-bold {{ $openingBalanceAmount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ₦{{ number_format(abs($openingBalanceAmount), 2) }}
                    <span class="text-sm">{{ $openingBalanceAmount >= 0 ? 'DR' : 'CR' }}</span>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="text-sm text-green-600 font-medium mb-1">Total Debits</div>
                <div class="text-2xl font-bold text-green-600">
                    ₦{{ number_format($totalDebits, 2) }}
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="text-sm text-red-600 font-medium mb-1">Total Credits</div>
                <div class="text-2xl font-bold text-red-600">
                    ₦{{ number_format($totalCredits, 2) }}
                </div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                <div class="text-sm text-purple-600 font-medium mb-1">Closing Balance</div>
                <div class="text-2xl font-bold {{ $closingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ₦{{ number_format(abs($closingBalance), 2) }}
                    <span class="text-sm">{{ $closingBalance >= 0 ? 'DR' : 'CR' }}</span>
                </div>
            </div>
        </div>

        <!-- Statement Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Transaction Details</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Statement Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </p>
            </div>

            @if(count($transactionsWithBalance) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Particulars</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voucher Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vch No.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit (₦)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit (₦)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance (₦)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Opening Balance Row -->
                            <tr class="bg-blue-50">
                                <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    Opening Balance
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold">
                                    @if($openingBalanceAmount > 0)
                                        {{ number_format($openingBalanceAmount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold">
                                    @if($openingBalanceAmount < 0)
                                        {{ number_format(abs($openingBalanceAmount), 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold {{ $openingBalanceAmount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format(abs($openingBalanceAmount), 2) }} {{ $openingBalanceAmount >= 0 ? 'DR' : 'CR' }}
                                </td>
                            </tr>

                            <!-- Transaction Rows -->
                            @foreach($transactionsWithBalance as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $transaction['particulars'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $transaction['voucher_type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction['voucher_number'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                        @if($transaction['debit'] > 0)
                                            {{ number_format($transaction['debit'], 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-red-600">
                                        @if($transaction['credit'] > 0)
                                            {{ number_format($transaction['credit'], 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold {{ $transaction['running_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format(abs($transaction['running_balance']), 2) }} {{ $transaction['running_balance'] >= 0 ? 'DR' : 'CR' }}
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Totals Row -->
                            <tr class="bg-gray-100 font-semibold">
                                <td colspan="4" class="px-6 py-4 text-sm text-gray-900">
                                    Total
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-green-600">
                                    {{ number_format($totalDebits, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-red-600">
                                    {{ number_format($totalCredits, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold {{ $closingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format(abs($closingBalance), 2) }} {{ $closingBalance >= 0 ? 'DR' : 'CR' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-5xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Transactions Found</h3>
                    <p class="text-gray-500">No transactions found for the selected period.</p>
                </div>
            @endif
        </div>

        <!-- Footer Note -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            <p class="mt-1">This is a system-generated statement and does not require a signature.</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-7xl, .max-w-7xl * {
            visibility: visible;
        }
        .max-w-7xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button, .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function downloadPDF() {
        // You can integrate with a PDF generation library or backend endpoint
        alert('PDF download functionality will be implemented.');
        // Future implementation:
        // window.location.href = "/{{ $tenant->slug }}/crm/customers/{{ $customer->id }}/statement/pdf?start_date={{ $startDate }}&end_date={{ $endDate }}";
    }
</script>
@endpush
@endsection
