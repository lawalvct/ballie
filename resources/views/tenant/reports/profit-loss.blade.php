@extends('layouts.tenant')

@section('title', 'Profit & Loss Statement')
@section('page-title', 'Profit & Loss Statement')
@section('page-description', 'View your profit and loss statement for the selected period')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" class="flex items-end space-x-4">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Generate Report
            </button>
        </form>
    </div>

    <!-- Report -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Profit & Loss Statement
            </h3>
            <p class="text-sm text-gray-600">
                Period: {{ date('M d, Y', strtotime($fromDate)) }} to {{ date('M d, Y', strtotime($toDate)) }}
            </p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Income -->
                <div>
                    <h4 class="text-lg font-medium text-green-800 mb-4 pb-2 border-b border-green-200">
                        Income
                    </h4>
                    <div class="space-y-3">
                        @forelse($incomeData as $item)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-700">{{ $item['account']->name }}</span>
                                <span class="font-medium text-green-600">
                                    ₦{{ number_format($item['amount'], 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">No income recorded</p>
                        @endforelse

                        <div class="border-t border-green-200 pt-3 mt-4">
                            <div class="flex justify-between items-center font-semibold text-green-800">
                                <span>Total Income</span>
                                <span>₦{{ number_format($totalIncome, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div>
                    <h4 class="text-lg font-medium text-red-800 mb-4 pb-2 border-b border-red-200">
                        Expenses
                    </h4>
                    <div class="space-y-3">
                        @forelse($expenseData as $item)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-700">{{ $item['account']->name }}</span>
                                <span class="font-medium text-red-600">
                                    ₦{{ number_format($item['amount'], 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">No expenses recorded</p>
                        @endforelse

                        <div class="border-t border-red-200 pt-3 mt-4">
                            <div class="flex justify-between items-center font-semibold text-red-800">
                                <span>Total Expenses</span>
                                <span>₦{{ number_format($totalExpenses, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Summary -->
            @if(isset($openingStock) || isset($closingStock))
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-medium text-gray-800 mb-4">Stock Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-sm text-blue-600 font-medium">Opening Stock</div>
                        <div class="text-2xl font-bold text-blue-800">₦{{ number_format($openingStock ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-sm text-green-600 font-medium">Closing Stock</div>
                        <div class="text-2xl font-bold text-green-800">₦{{ number_format($closingStock ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Net Profit/Loss -->
            <div class="mt-8 pt-6 border-t-2 border-gray-300">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-900">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
                    <span class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ₦{{ number_format(abs($netProfit), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection