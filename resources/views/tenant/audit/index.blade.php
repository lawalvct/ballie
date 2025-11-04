@extends('layouts.tenant')

@section('title', 'Audit Trail')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🔍 Audit Trail</h1>
                <p class="text-gray-600 mt-1">Track all user activities and system changes</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('tenant.audit.export', ['tenant' => $tenant->slug]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-download mr-2"></i>Export Report
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Records</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_records']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Created Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['created_today']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Updated Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['updated_today']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Posted Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['posted_today']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
        <form method="GET" action="{{ route('tenant.audit.index', ['tenant' => $tenant->slug]) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $userFilter == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Actions</option>
                    <option value="created" {{ $actionFilter == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ $actionFilter == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ $actionFilter == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="posted" {{ $actionFilter == 'posted' ? 'selected' : '' }}>Posted</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Model Type</label>
                <select name="model" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Types</option>
                    <option value="customer" {{ $modelFilter == 'customer' ? 'selected' : '' }}>Customers</option>
                    <option value="vendor" {{ $modelFilter == 'vendor' ? 'selected' : '' }}>Vendors</option>
                    <option value="product" {{ $modelFilter == 'product' ? 'selected' : '' }}>Products</option>
                    <option value="voucher" {{ $modelFilter == 'voucher' ? 'selected' : '' }}>Vouchers</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="col-span-full flex justify-end space-x-3">
                <a href="{{ route('tenant.audit.index', ['tenant' => $tenant->slug]) }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Clear Filters
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Activities Timeline -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activities</h2>
        </div>

        <div class="p-6">
            @if($activities->count() > 0)
                <div class="space-y-4">
                    @foreach($activities as $activity)
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $activity['action'] == 'created' ? 'bg-green-100 text-green-600' : '' }}
                                    {{ $activity['action'] == 'updated' ? 'bg-yellow-100 text-yellow-600' : '' }}
                                    {{ $activity['action'] == 'deleted' ? 'bg-red-100 text-red-600' : '' }}
                                    {{ $activity['action'] == 'posted' ? 'bg-purple-100 text-purple-600' : '' }}">
                                    @if($activity['action'] == 'created')
                                        <i class="fas fa-plus"></i>
                                    @elseif($activity['action'] == 'updated')
                                        <i class="fas fa-edit"></i>
                                    @elseif($activity['action'] == 'deleted')
                                        <i class="fas fa-trash"></i>
                                    @elseif($activity['action'] == 'posted')
                                        <i class="fas fa-check"></i>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $activity['details'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            By: <span class="font-medium">{{ $activity['user']->name ?? 'System' }}</span>
                                            <span class="mx-2">•</span>
                                            <span class="text-gray-500">{{ $activity['model'] }}</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-3 ml-4">
                                        <span class="text-sm text-gray-500">
                                            {{ $activity['timestamp']->format('M d, Y H:i') }}
                                        </span>
                                        @if($activity['model'] && $activity['id'])
                                            <a href="{{ route('tenant.audit.show', ['tenant' => $tenant->slug, 'model' => strtolower($activity['model']), 'id' => $activity['id']]) }}"
                                               class="text-indigo-600 hover:text-indigo-700">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-history text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-600">No activities found matching your filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
