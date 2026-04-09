@extends('layouts.tenant')

@section('title', 'Prepaid Expense Schedule - ' . $tenant->name)
@section('page-title', 'Prepaid Expense Schedule')
@section('page-description', $prepaidExpense->description ?: 'Amortization schedule details')

@section('content')
<div class="space-y-6">
    {{-- Back + Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.accounting.prepaid-expenses.index', $tenant->slug) }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
        <div class="flex items-center space-x-2">
            @if($prepaidExpense->status === 'active')
                <form method="POST" action="{{ route('tenant.accounting.prepaid-expenses.pause', [$tenant->slug, $prepaidExpense->id]) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pause
                    </button>
                </form>
                <form method="POST" action="{{ route('tenant.accounting.prepaid-expenses.cancel', [$tenant->slug, $prepaidExpense->id]) }}"
                      onsubmit="return confirm('Cancel this prepaid schedule? No further installments will be posted.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cancel Schedule
                    </button>
                </form>
            @elseif($prepaidExpense->status === 'paused')
                <form method="POST" action="{{ route('tenant.accounting.prepaid-expenses.resume', [$tenant->slug, $prepaidExpense->id]) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-100 border border-green-300 rounded-lg hover:bg-green-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Resume
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $prepaidExpense->description ?: 'Prepaid Expense' }}</h3>
                        <p class="text-sm text-gray-500">Created from {{ $prepaidExpense->voucher?->getDisplayNumber() }}</p>
                    </div>
                </div>
                @php
                    $statusColors = [
                        'active' => 'bg-green-100 text-green-800',
                        'paused' => 'bg-yellow-100 text-yellow-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$prepaidExpense->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($prepaidExpense->status) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Amount</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($prepaidExpense->total_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Per Installment</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($prepaidExpense->installment_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Remaining</p>
                    <p class="text-xl font-bold text-amber-600 mt-1">₦{{ number_format($prepaidExpense->getRemainingAmount(), 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Progress</p>
                    <div class="mt-2">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $prepaidExpense->getProgressPercentage() }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $prepaidExpense->installments_posted }}/{{ $prepaidExpense->installments_count }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6 pt-6 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">Expense Account</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $prepaidExpense->expenseAccount?->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Prepaid Asset Account</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $prepaidExpense->prepaidAccount?->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Frequency</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ ucfirst($prepaidExpense->frequency) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Period</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">
                        {{ $prepaidExpense->start_date->format('M d, Y') }} &mdash; {{ $prepaidExpense->end_date->format('M d, Y') }}
                    </p>
                </div>
            </div>

            @if($prepaidExpense->next_posting_date)
                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Next installment of <strong>₦{{ number_format($prepaidExpense->getNextInstallmentAmount(), 2) }}</strong> will be posted on <strong>{{ $prepaidExpense->next_posting_date->format('M d, Y') }}</strong>
                </div>
            @endif
        </div>
    </div>

    {{-- Original Voucher Link --}}
    @if($prepaidExpense->voucher)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Original Payment Voucher</h4>
            <a href="{{ route('tenant.accounting.vouchers.show', [$tenant->slug, $prepaidExpense->voucher_id]) }}"
               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                {{ $prepaidExpense->voucher->getDisplayNumber() }} &mdash; ₦{{ number_format($prepaidExpense->voucher->total_amount, 2) }}
                ({{ $prepaidExpense->voucher->voucher_date->format('M d, Y') }})
            </a>
        </div>
    @endif

    {{-- Amortization Schedule Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Amortization Schedule</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schedule as $row)
                        @php
                            $posting = $prepaidExpense->postings->firstWhere('installment_number', $row['installment_number']);
                        @endphp
                        <tr class="{{ $row['status'] === 'posted' ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $row['installment_number'] }}</td>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($row['date'])->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-sm text-right font-medium text-gray-900">₦{{ number_format($row['amount'], 2) }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($row['status'] === 'posted')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Posted</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm">
                                @if($posting && $posting->voucher)
                                    <a href="{{ route('tenant.accounting.vouchers.show', [$tenant->slug, $posting->voucher_id]) }}"
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $posting->voucher->getDisplayNumber() }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-sm font-semibold text-gray-900 text-right">Total:</td>
                        <td class="px-6 py-3 text-sm text-right font-bold text-gray-900">₦{{ number_format($prepaidExpense->total_amount, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
