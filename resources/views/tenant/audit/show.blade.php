@extends('layouts.tenant')

@section('title', 'Audit Trail Details')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('tenant.audit.index', ['tenant' => $tenant->slug]) }}" class="text-indigo-600 hover:text-indigo-700 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Audit Trail
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Audit Trail Details</h1>
                <p class="text-gray-600 mt-1">Complete history for {{ ucfirst($modelType) }} #{{ $recordId }}</p>
            </div>
        </div>
    </div>

    <!-- Record Information Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Record Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Model Type</p>
                <p class="text-base font-medium text-gray-900">{{ ucfirst($modelType) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Record ID</p>
                <p class="text-base font-medium text-gray-900">#{{ $recordId }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Activities</p>
                <p class="text-base font-medium text-gray-900">{{ count($auditTrail) }}</p>
            </div>
        </div>
    </div>

    <!-- Audit Timeline -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Activity Timeline</h2>
        </div>

        <div class="p-6">
            @if(count($auditTrail) > 0)
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                    <!-- Timeline Items -->
                    <div class="space-y-6">
                        @foreach($auditTrail as $index => $activity)
                            <div class="relative flex items-start space-x-4">
                                <!-- Timeline Dot -->
                                <div class="relative z-10 flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $activity['action'] == 'created' ? 'bg-green-500 text-white' : '' }}
                                        {{ $activity['action'] == 'updated' ? 'bg-yellow-500 text-white' : '' }}
                                        {{ $activity['action'] == 'deleted' ? 'bg-red-500 text-white' : '' }}
                                        {{ $activity['action'] == 'posted' ? 'bg-purple-500 text-white' : '' }}
                                        shadow-lg">
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

                                <!-- Activity Content -->
                                <div class="flex-1 bg-gray-50 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                    {{ $activity['action'] == 'created' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $activity['action'] == 'updated' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $activity['action'] == 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $activity['action'] == 'posted' ? 'bg-purple-100 text-purple-800' : '' }}">
                                                    {{ ucfirst($activity['action']) }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    {{ $activity['timestamp']->format('F d, Y') }} at {{ $activity['timestamp']->format('h:i A') }}
                                                </span>
                                            </div>

                                            <p class="text-base font-medium text-gray-900 mb-2">
                                                {{ $activity['details'] }}
                                            </p>

                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <div class="flex items-center">
                                                    <i class="fas fa-user mr-2"></i>
                                                    <span>{{ $activity['user']->name ?? 'System' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-envelope mr-2"></i>
                                                    <span>{{ $activity['user']->email ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right ml-4">
                                            <span class="text-xs text-gray-500">
                                                {{ $activity['timestamp']->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-history text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-600">No audit trail available for this record.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-3">
        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-print mr-2"></i>Print Timeline
        </button>
        <a href="{{ route('tenant.audit.index', ['tenant' => $tenant->slug]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-list mr-2"></i>View All Activities
        </a>
    </div>
</div>
@endsection
