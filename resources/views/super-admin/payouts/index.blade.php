@extends('layouts.super-admin')

@section('title', 'Payout Requests')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-4">
            <p class="text-xs font-semibold text-yellow-700 uppercase">Pending</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-4">
            <p class="text-xs font-semibold text-blue-700 uppercase">Approved</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-purple-200 p-4">
            <p class="text-xs font-semibold text-purple-700 uppercase">Processing</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['processing'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-green-200 p-4">
            <p class="text-xs font-semibold text-green-700 uppercase">Completed</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-600 uppercase">Pending Amount</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($stats['total_pending_amount'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-600 uppercase">Paid Out</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($stats['total_completed_amount'], 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tenant</label>
                <select name="tenant_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filter</button>
                <a href="{{ route('super-admin.payouts.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800">Payout Requests</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('super-admin.payouts.settings') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Settings</a>
                <a href="{{ route('super-admin.payouts.export', request()->all()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Export</a>
            </div>
        </div>

        @if($payouts->isEmpty())
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4" />
                    </svg>
                    <h4 class="text-lg font-medium text-gray-700 mb-2">No Payout Requests</h4>
                    <p class="text-gray-500 text-sm">No payout requests match your criteria.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
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
                        @foreach($payouts as $payout)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('super-admin.payouts.show', $payout) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    {{ $payout->request_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $payout->tenant->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $payout->requester->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₦{{ number_format($payout->requested_amount, 2) }}</div>
                                <div class="text-xs text-green-600">Net: ₦{{ number_format($payout->net_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $payout->bank_name }}</div>
                                <div class="text-xs text-gray-500">{{ $payout->account_number }} - {{ $payout->account_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$payout->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $payout->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payout->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $payout->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('super-admin.payouts.show', $payout) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if($payout->status === 'pending')
                                    <form action="{{ route('super-admin.payouts.approve', $payout) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payouts->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection@extends('layouts.super-admin')

@section('title', 'Payout Requests')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-4 border-warning h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">Pending</div>
                    <div class="h4 mb-0">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-4 border-info h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">Approved</div>
                    <div class="h4 mb-0">{{ $stats['approved'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-4 border-primary h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">Processing</div>
                    <div class="h4 mb-0">{{ $stats['processing'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">Completed</div>
                    <div class="h4 mb-0">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-4 border-secondary h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">Pending Amount</div>
                    <div class="h5 mb-0">₦{{ number_format($stats['total_pending_amount'], 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-start border-4 border-dark h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">Paid Out</div>
                    <div class="h5 mb-0">₦{{ number_format($stats['total_completed_amount'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tenant_id" class="form-select">
                        <option value="">All Tenants</option>
                        @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary me-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('super-admin.payouts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Payout Requests</h5>
            <div>
                <a href="{{ route('super-admin.payouts.settings') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-cog me-1"></i> Settings
                </a>
                <a href="{{ route('super-admin.payouts.export', request()->all()) }}" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($payouts->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No Payout Requests</h5>
                <p class="text-muted">No payout requests match your criteria.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Request #</th>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Bank Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                        <tr>
                            <td>
                                <a href="{{ route('super-admin.payouts.show', $payout) }}" class="fw-bold text-decoration-none">
                                    {{ $payout->request_number }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $payout->tenant->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $payout->requester->name ?? '' }}</small>
                            </td>
                            <td>
                                <div>₦{{ number_format($payout->requested_amount, 2) }}</div>
                                <small class="text-success">Net: ₦{{ number_format($payout->net_amount, 2) }}</small>
                            </td>
                            <td>
                                <div>{{ $payout->bank_name }}</div>
                                <small class="text-muted">{{ $payout->account_number }} - {{ $payout->account_name }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $payout->status_color }}">{{ $payout->status_label }}</span>
                            </td>
                            <td>
                                <div>{{ $payout->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $payout->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('super-admin.payouts.show', $payout) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($payout->status === 'pending')
                                    <form action="{{ route('super-admin.payouts.approve', $payout) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $payouts->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
