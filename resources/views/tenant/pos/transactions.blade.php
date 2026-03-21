@extends('layouts.tenant')

@section('title', 'Transaction History - ' . $tenant->name)

@section('page-title', 'POS Transactions')
@section('page-description')
    <span class="hidden md:inline">View, search, and manage all POS sales transactions.</span>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transaction History</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $sales->total() }} total transactions</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.pos.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--color-dark-purple)] hover:bg-[var(--color-purple-light)] text-white rounded-lg font-medium text-sm transition-colors duration-200 shadow-sm">
                    <i class="fas fa-cash-register"></i>
                    <span>Back to POS</span>
                </a>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Sale #</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">Cashier</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)]">
                                    {{ $sale->sale_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $sale->sale_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->sale_date->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sale->customer)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-[var(--color-dark-purple)]/10 dark:bg-[var(--color-purple-accent)]/20 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)] text-xs"></i>
                                        </div>
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $sale->customer->full_name ?? $sale->customer->company_name ?? 'Customer' }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">Walk-in</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $sale->user->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = match($sale->status ?? 'completed') {
                                        'completed' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'icon' => 'fa-check-circle', 'label' => 'Completed'],
                                        'voided'    => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'icon' => 'fa-ban', 'label' => 'Voided'],
                                        'refunded'  => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-700 dark:text-yellow-400', 'icon' => 'fa-undo', 'label' => 'Refunded'],
                                        'pending'   => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'icon' => 'fa-clock', 'label' => 'Pending'],
                                        default     => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'icon' => 'fa-question-circle', 'label' => ucfirst($sale->status ?? 'unknown')],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <i class="fas {{ $statusConfig['icon'] }} text-[0.65rem]"></i>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">₦{{ number_format($sale->total_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('tenant.pos.receipt', ['tenant' => $tenant->slug, 'sale' => $sale->id]) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                       title="View Receipt" target="_blank">
                                        <i class="fas fa-receipt text-sm"></i>
                                    </a>
                                    @if(($sale->status ?? 'completed') === 'completed')
                                        <button onclick="if(confirm('Are you sure you want to void sale {{ $sale->sale_number }}? This cannot be undone.')) { voidSale({{ $sale->id }}) }"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                                                title="Void Sale">
                                            <i class="fas fa-ban text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-receipt text-gray-400 dark:text-gray-500 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No Transactions Yet</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sales transactions will appear here once you start selling.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($sales as $sale)
                <div class="p-4 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)]">{{ $sale->sale_number }}</span>
                        @php
                            $mobileStatus = match($sale->status ?? 'completed') {
                                'completed' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'label' => 'Completed'],
                                'voided'    => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Voided'],
                                'refunded'  => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-700 dark:text-yellow-400', 'label' => 'Refunded'],
                                default     => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-700 dark:text-gray-300', 'label' => ucfirst($sale->status ?? 'unknown')],
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $mobileStatus['bg'] }} {{ $mobileStatus['text'] }}">
                            {{ $mobileStatus['label'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                @if($sale->customer)
                                    {{ $sale->customer->full_name ?? $sale->customer->company_name ?? 'Customer' }}
                                @else
                                    <span class="italic text-gray-400">Walk-in</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->sale_date->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <span class="text-base font-bold text-gray-900 dark:text-white">₦{{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
                        <a href="{{ route('tenant.pos.receipt', ['tenant' => $tenant->slug, 'sale' => $sale->id]) }}"
                           class="flex-1 text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium py-1"
                           target="_blank">
                            <i class="fas fa-receipt mr-1"></i> Receipt
                        </a>
                        @if(($sale->status ?? 'completed') === 'completed')
                            <button onclick="if(confirm('Are you sure you want to void this sale?')) { voidSale({{ $sale->id }}) }"
                                    class="flex-1 text-center text-sm text-red-500 dark:text-red-400 hover:text-red-600 font-medium py-1">
                                <i class="fas fa-ban mr-1"></i> Void
                            </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-receipt text-gray-400 dark:text-gray-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No Transactions Yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sales will appear here once you start selling.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($sales->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
async function voidSale(saleId) {
    try {
        const response = await fetch(`{{ url($tenant->slug . '/pos') }}/${saleId}/void`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        const result = await response.json();
        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message || 'Failed to void sale.');
        }
    } catch (error) {
        alert('Failed to void sale. Please try again.');
    }
}
</script>
@endsection
