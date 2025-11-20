@extends('layouts.tenant')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Balance Sheet (DR/CR Format)</h1>
            <p class="text-sm text-gray-500">As of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('tenant.accounting.balance-sheet', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Modern View</a>
            <a href="{{ route('tenant.accounting.balance-sheet-table', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Table View</a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="grid grid-cols-2 gap-0">
            <!-- Debit Side -->
            <div class="border-r border-gray-200">
                <div class="bg-blue-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-800">DEBIT (DR)</h3>
                </div>
                <div class="divide-y divide-gray-200 min-h-[300px]">
                    @forelse($debitSide as $item)
                    <div class="px-6 py-3 flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-900">{{ $item['account']->name }}</div>
                            <div class="text-sm text-gray-500">{{ $item['type'] }} @if(!empty($item['account']->code))- {{ $item['account']->code }}@endif</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-gray-900">₦{{ number_format($item['balance'], 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-3 text-center text-gray-500">
                        No debit entries
                    </div>
                    @endforelse
                </div>
                <div class="bg-blue-100 px-6 py-4 border-t-2 border-blue-300 mt-auto">
                    <div class="flex justify-between items-center">
                        <div class="font-bold text-blue-900 text-lg">Total Debits</div>
                        <div class="font-bold text-blue-900 text-lg">₦{{ number_format($totalDebits, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Credit Side -->
            <div>
                <div class="bg-red-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-red-800">CREDIT (CR)</h3>
                </div>
                <div class="divide-y divide-gray-200 min-h-[300px]">
                    @forelse($creditSide as $item)
                    <div class="px-6 py-3 flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-900">{{ $item['account']->name }}</div>
                            <div class="text-sm text-gray-500">{{ $item['type'] }} @if(!empty($item['account']->code))- {{ $item['account']->code }}@endif</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-gray-900">₦{{ number_format($item['balance'], 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-3 text-center text-gray-500">
                        No credit entries
                    </div>
                    @endforelse
                </div>
                <div class="bg-red-100 px-6 py-4 border-t-2 border-red-300 mt-auto">
                    <div class="flex justify-between items-center">
                        <div class="font-bold text-red-900 text-lg">Total Credits</div>
                        <div class="font-bold text-red-900 text-lg">₦{{ number_format($totalCredits, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Check -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">Balance Verification:</div>
                <div>
                    @if($balanceCheck)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Ballie (DR = CR)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ✗ Out of Balance (Difference: ₦{{ number_format(abs($totalDebits - $totalCredits), 2) }})
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Accounting Equation -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-sm font-semibold text-blue-800 mb-2">Accounting Equation Verification:</h4>
        <p class="text-sm text-blue-700">
            <strong>Assets (DR) = Liabilities (CR) + Equity (CR)</strong><br>
            In DR/CR format: Total Debits should equal Total Credits
        </p>
    </div>
</div>
@endsection
