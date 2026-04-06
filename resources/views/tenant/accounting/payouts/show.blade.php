@extends('layouts.tenant')

@section('title', 'Withdrawal Request ' . $payout->request_number)
@section('page-title', 'Withdrawal Request ' . $payout->request_number)
@section('page-description', 'View details of your withdrawal request')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Link -->
    <div>
        <a href="{{ route('tenant.accounting.payouts.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Withdrawals
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Status Banner -->
    @php
        $statusConfig = match($payout->status) {
            'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'icon' => 'text-yellow-500', 'label' => 'Pending Review'],
            'approved' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'icon' => 'text-blue-500', 'label' => 'Approved'],
            'processing' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-800', 'icon' => 'text-indigo-500', 'label' => 'Processing'],
            'completed' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'text-green-500', 'label' => 'Completed'],
            'rejected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'icon' => 'text-red-500', 'label' => 'Rejected'],
            'cancelled' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'icon' => 'text-gray-500', 'label' => 'Cancelled'],
            default => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-800', 'icon' => 'text-gray-500', 'label' => ucfirst($payout->status)],
        };
    @endphp
    <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['border'] }} border rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="{{ $statusConfig['icon'] }} mr-3">
                    @if($payout->status === 'completed')
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @elseif($payout->status === 'rejected')
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    @else
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    @endif
                </div>
                <div>
                    <p class="{{ $statusConfig['text'] }} text-lg font-bold">{{ $statusConfig['label'] }}</p>
                    <p class="{{ $statusConfig['text'] }} text-sm opacity-75">Request {{ $payout->request_number }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="{{ $statusConfig['text'] }} text-2xl font-bold">₦{{ number_format($payout->net_amount, 2) }}</p>
                <p class="{{ $statusConfig['text'] }} text-xs opacity-75">Net amount to receive</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="w-full bg-white/60 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-500
                    @if($payout->status === 'completed') bg-green-500
                    @elseif($payout->status === 'rejected' || $payout->status === 'cancelled') bg-red-400
                    @else bg-blue-500
                    @endif"
                    style="width: {{ $payout->progress_percentage }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs {{ $statusConfig['text'] }} opacity-75">
                <span>Submitted</span>
                <span>Approved</span>
                <span>Processing</span>
                <span>Completed</span>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Request Details</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm text-gray-500">Request Number</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->request_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Date Submitted</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->created_at->format('M d, Y g:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Requested Amount</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">₦{{ number_format($payout->requested_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">{{ $payout->deduction_description }}</dt>
                    <dd class="mt-1 text-sm font-medium text-red-600">-₦{{ number_format($payout->deduction_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Net Amount</dt>
                    <dd class="mt-1 text-lg font-bold text-green-600">₦{{ number_format($payout->net_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Available Balance (at time of request)</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">₦{{ number_format($payout->available_balance, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Bank Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Bank Details</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm text-gray-500">Bank Name</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->bank_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Account Number</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->account_number }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm text-gray-500">Account Name</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->account_name }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Processing Info (if processed) -->
    @if($payout->processed_at || $payout->admin_notes || $payout->rejection_reason || $payout->payment_reference)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Processing Details</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                @if($payout->processed_at)
                <div>
                    <dt class="text-sm text-gray-500">Processed At</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->processed_at->format('M d, Y g:i A') }}</dd>
                </div>
                @endif
                @if($payout->payment_reference)
                <div>
                    <dt class="text-sm text-gray-500">Payment Reference</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payout->payment_reference }}</dd>
                </div>
                @endif
                @if($payout->admin_notes)
                <div class="sm:col-span-2">
                    <dt class="text-sm text-gray-500">Admin Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $payout->admin_notes }}</dd>
                </div>
                @endif
                @if($payout->rejection_reason)
                <div class="sm:col-span-2">
                    <dt class="text-sm text-gray-500">Rejection Reason</dt>
                    <dd class="mt-1 text-sm text-red-600">{{ $payout->rejection_reason }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($payout->notes)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Your Notes</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-700">{{ $payout->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($payout->canBeCancelled())
    <div class="flex justify-end">
        <form method="POST" action="{{ route('tenant.accounting.payouts.cancel', ['tenant' => $tenant->slug, 'payout' => $payout->id]) }}"
              onsubmit="return confirm('Are you sure you want to cancel this withdrawal request?')">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel Request
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
