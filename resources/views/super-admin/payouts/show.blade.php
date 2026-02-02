@extends('layouts.super-admin')

@section('title', 'Payout Details - ' . $payout->request_number)

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('super-admin.payouts.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Payouts
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-lg font-semibold text-gray-800">Payout #{{ $payout->request_number }}</h3>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'processing' => 'bg-purple-100 text-purple-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$payout->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $payout->status_label }}
                    </span>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Tenant</p>
                            <p class="text-base font-semibold text-gray-800">{{ $payout->tenant->name ?? 'Unknown Tenant' }}</p>
                            <p class="text-sm text-gray-500">Requested by: {{ $payout->requester->name ?? 'N/A' }} @if($payout->requester?->email)| {{ $payout->requester->email }}@endif</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">Requested Amount</p>
                            <p class="text-2xl font-bold text-gray-800">₦{{ number_format($payout->requested_amount, 2) }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">{{ $payout->deduction_description ?: 'Deduction' }}</p>
                            <p class="text-2xl font-bold text-red-600">-₦{{ number_format($payout->deduction_amount, 2) }}</p>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">Net Amount (To Pay)</p>
                            <p class="text-2xl font-bold text-green-600">₦{{ number_format($payout->net_amount, 2) }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Bank Details</h4>
                        <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                            <table class="w-full">
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600 w-1/3">Bank Name</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">{{ $payout->bank_name }}</span>
                                                <button type="button" class="text-blue-600 hover:text-blue-800" onclick="copyToClipboard('{{ $payout->bank_name }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V5a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600">Account Number</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-semibold">{{ $payout->account_number }}</span>
                                                <button type="button" class="text-blue-600 hover:text-blue-800" onclick="copyToClipboard('{{ $payout->account_number }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V5a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600">Account Name</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">{{ $payout->account_name }}</span>
                                                <button type="button" class="text-blue-600 hover:text-blue-800" onclick="copyToClipboard('{{ $payout->account_name }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V5a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($payout->notes)
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Tenant Notes</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700">{{ $payout->notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($payout->payment_reference)
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                        <p class="text-sm font-medium text-green-800">Payment Reference</p>
                        <p class="text-sm text-green-700 font-mono">{{ $payout->payment_reference }}</p>
                    </div>
                    @endif

                    @if($payout->rejection_reason)
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                        <p class="text-sm font-medium text-red-800">Rejection Reason</p>
                        <p class="text-sm text-red-700">{{ $payout->rejection_reason }}</p>
                    </div>
                    @endif

                    @if($payout->admin_notes)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                        <p class="text-sm font-medium text-blue-800">Admin Notes</p>
                        <p class="text-sm text-blue-700">{{ $payout->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if(in_array($payout->status, ['pending', 'approved', 'processing']))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Actions</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($payout->status === 'pending')
                        <form action="{{ route('super-admin.payouts.approve', $payout) }}" method="POST" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            @csrf
                            @method('PATCH')
                            <h5 class="text-sm font-semibold text-green-800 mb-2">Approve Request</h5>
                            <textarea name="admin_notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" rows="2" placeholder="Optional notes..."></textarea>
                            <button type="submit" class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Approve</button>
                        </form>

                        <form action="{{ route('super-admin.payouts.reject', $payout) }}" method="POST" class="bg-red-50 border border-red-200 rounded-lg p-4">
                            @csrf
                            @method('PATCH')
                            <h5 class="text-sm font-semibold text-red-800 mb-2">Reject Request</h5>
                            <textarea name="rejection_reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="2" placeholder="Reason for rejection..." required></textarea>
                            <button type="submit" class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Reject</button>
                        </form>
                        @endif

                        @if($payout->status === 'approved')
                        <form action="{{ route('super-admin.payouts.processing', $payout) }}" method="POST" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            @csrf
                            @method('PATCH')
                            <h5 class="text-sm font-semibold text-blue-800 mb-2">Mark as Processing</h5>
                            <p class="text-sm text-blue-700">Click when you've started the bank transfer.</p>
                            <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Mark Processing</button>
                        </form>
                        @endif

                        @if(in_array($payout->status, ['approved', 'processing']))
                        <form action="{{ route('super-admin.payouts.complete', $payout) }}" method="POST" class="bg-green-50 border border-green-200 rounded-lg p-4">
                            @csrf
                            @method('PATCH')
                            <h5 class="text-sm font-semibold text-green-800 mb-2">Mark as Completed</h5>
                            <input type="text" name="payment_reference" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Bank transfer reference *" required>
                            <textarea name="admin_notes" class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" rows="2" placeholder="Optional notes..."></textarea>
                            <button type="submit" class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Complete Payout</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Request Info</h4>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Request #</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->request_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Status</dt>
                            <dd>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$payout->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $payout->status_label }}</span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Submitted</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        @if($payout->processed_at)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Processed</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->processed_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        @endif
                        @if($payout->processor)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Processed By</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->processor->name ?? 'N/A' }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($tenantPayoutHistory->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Tenant History</h4>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($tenantPayoutHistory as $history)
                    <a href="{{ route('super-admin.payouts.show', $history) }}" class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $history->request_number }}</p>
                                <p class="text-xs text-gray-500">₦{{ number_format($history->net_amount, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$history->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $history->status_label }}</span>
                                <p class="text-xs text-gray-500">{{ $history->created_at->format('M d') }}</p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 text-center">
                <h4 class="text-md font-semibold text-gray-800 mb-2">Quick Copy</h4>
                <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition" onclick="copyAll()">
                    Copy All Bank Details
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied: ' + text);
    });
}

function copyAll() {
    const details = `Bank: {{ $payout->bank_name }}
Account Number: {{ $payout->account_number }}
Account Name: {{ $payout->account_name }}
Amount: ₦{{ number_format($payout->net_amount, 2) }}`;

    navigator.clipboard.writeText(details).then(() => {
        alert('All bank details copied!');
    });
}
</script>
@endpush
@endsection

@section('title', 'Payout Details - ' . $payout->request_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <a href="{{ route('super-admin.payouts.index') }}" class="text-muted me-2">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        Payout #{{ $payout->request_number }}
                    </h5>
                    <span class="badge bg-{{ $payout->status_color }} fs-6">{{ $payout->status_label }}</span>
                </div>
                <div class="card-body">
                    <!-- Tenant Info -->
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $payout->tenant->name ?? 'Unknown Tenant' }}</h6>
                            <small class="text-muted">
                                Requested by: {{ $payout->requester->name ?? 'N/A' }} |
                                {{ $payout->requester->email ?? '' }}
                            </small>
                        </div>
                    </div>

                    <!-- Amount Details -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Requested Amount</small>
                                <div class="h4 mb-0">₦{{ number_format($payout->requested_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center border-danger">
                                <small class="text-muted">{{ $payout->deduction_description ?: 'Deduction' }}</small>
                                <div class="h4 mb-0 text-danger">-₦{{ number_format($payout->deduction_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-success bg-opacity-10 border-success">
                                <small class="text-muted">Net Amount (To Pay)</small>
                                <div class="h4 mb-0 text-success">₦{{ number_format($payout->net_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <h6 class="mb-3"><i class="fas fa-university me-1"></i> Bank Details</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th width="30%" class="bg-light">Bank Name</th>
                                <td>
                                    <strong>{{ $payout->bank_name }}</strong>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('{{ $payout->bank_name }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Account Number</th>
                                <td>
                                    <strong class="font-monospace">{{ $payout->account_number }}</strong>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('{{ $payout->account_number }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Account Name</th>
                                <td>
                                    <strong>{{ $payout->account_name }}</strong>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('{{ $payout->account_name }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    @if($payout->notes)
                    <h6 class="mb-3"><i class="fas fa-sticky-note me-1"></i> Tenant Notes</h6>
                    <p class="bg-light p-3 rounded">{{ $payout->notes }}</p>
                    @endif

                    @if($payout->payment_reference)
                    <div class="alert alert-success">
                        <strong><i class="fas fa-receipt me-1"></i> Payment Reference:</strong>
                        {{ $payout->payment_reference }}
                    </div>
                    @endif

                    @if($payout->rejection_reason)
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-times-circle me-1"></i> Rejection Reason:</strong>
                        {{ $payout->rejection_reason }}
                    </div>
                    @endif

                    @if($payout->admin_notes)
                    <div class="alert alert-info">
                        <strong><i class="fas fa-comment me-1"></i> Admin Notes:</strong>
                        {{ $payout->admin_notes }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Cards -->
            @if(in_array($payout->status, ['pending', 'approved', 'processing']))
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-tasks me-1"></i> Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($payout->status === 'pending')
                        <!-- Approve -->
                        <div class="col-md-6">
                            <form action="{{ route('super-admin.payouts.approve', $payout) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-success"><i class="fas fa-check me-1"></i> Approve Request</h6>
                                        <div class="mb-3">
                                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Reject -->
                        <div class="col-md-6">
                            <form action="{{ route('super-admin.payouts.reject', $payout) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card border-danger h-100">
                                    <div class="card-body">
                                        <h6 class="text-danger"><i class="fas fa-times me-1"></i> Reject Request</h6>
                                        <div class="mb-3">
                                            <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Reason for rejection..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if($payout->status === 'approved')
                        <!-- Mark Processing -->
                        <div class="col-md-6">
                            <form action="{{ route('super-admin.payouts.processing', $payout) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary"><i class="fas fa-cog me-1"></i> Mark as Processing</h6>
                                        <p class="text-muted small">Click when you've started the bank transfer.</p>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-cog me-1"></i> Mark Processing
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if(in_array($payout->status, ['approved', 'processing']))
                        <!-- Complete -->
                        <div class="col-md-6">
                            <form action="{{ route('super-admin.payouts.complete', $payout) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-success"><i class="fas fa-check-double me-1"></i> Mark as Completed</h6>
                                        <div class="mb-3">
                                            <input type="text" name="payment_reference" class="form-control" placeholder="Bank transfer reference *" required>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-double me-1"></i> Complete Payout
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Request Info -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-info-circle me-1"></i> Request Info</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Request #</td>
                            <td class="fw-bold">{{ $payout->request_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $payout->status_color }}">{{ $payout->status_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Submitted</td>
                            <td>{{ $payout->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @if($payout->processed_at)
                        <tr>
                            <td class="text-muted">Processed</td>
                            <td>{{ $payout->processed_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @endif
                        @if($payout->processor)
                        <tr>
                            <td class="text-muted">Processed By</td>
                            <td>{{ $payout->processor->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Tenant Payout History -->
            @if($tenantPayoutHistory->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-history me-1"></i> Tenant History</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($tenantPayoutHistory as $history)
                        <a href="{{ route('super-admin.payouts.show', $history) }}" class="list-group-item list-group-item-action px-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-bold">{{ $history->request_number }}</span><br>
                                    <small class="text-muted">₦{{ number_format($history->net_amount, 2) }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $history->status_color }}">{{ $history->status_label }}</span><br>
                                    <small class="text-muted">{{ $history->created_at->format('M d') }}</small>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Copy -->
            <div class="card mt-3 bg-light">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-copy me-1"></i> Quick Copy</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyAll()">
                            <i class="fas fa-copy me-1"></i> Copy All Bank Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast or feedback
        alert('Copied: ' + text);
    });
}

function copyAll() {
    const details = `Bank: {{ $payout->bank_name }}
Account Number: {{ $payout->account_number }}
Account Name: {{ $payout->account_name }}
Amount: ₦{{ number_format($payout->net_amount, 2) }}`;

    navigator.clipboard.writeText(details).then(() => {
        alert('All bank details copied!');
    });
}
</script>
@endpush
@endsection
