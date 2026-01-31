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
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Activity Timeline</h2>
                <p class="text-sm text-gray-500">Most recent activity first</p>
            </div>
            <div class="text-xs text-gray-400">
                {{ count($auditTrail) }} events
            </div>
        </div>

        <div class="p-6">
            @if(count($auditTrail) > 0)
                <div class="relative">
                    <div class="absolute left-4 sm:left-6 top-2 bottom-2 w-px bg-gradient-to-b from-gray-200 via-gray-200 to-transparent"></div>

                    <div class="space-y-6">
                        @foreach($auditTrail as $index => $activity)
                            @php
                                $actionBadge = match($activity['action'] ?? '') {
                                    'created' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'icon' => 'fa-plus'],
                                    'updated' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'ring' => 'ring-amber-200', 'icon' => 'fa-pen'],
                                    'deleted' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200', 'icon' => 'fa-trash'],
                                    'posted' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-700', 'ring' => 'ring-violet-200', 'icon' => 'fa-check'],
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'ring' => 'ring-gray-200', 'icon' => 'fa-circle'],
                                };
                            @endphp
                            <div class="relative flex gap-4 sm:gap-6">
                                <div class="relative z-10 flex-shrink-0">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center {{ $actionBadge['bg'] }} {{ $actionBadge['text'] }} ring-4 ring-white shadow">
                                        <i class="fas {{ $actionBadge['icon'] }} text-sm"></i>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                        <div class="p-4 sm:p-5">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $actionBadge['bg'] }} {{ $actionBadge['text'] }} {{ $actionBadge['ring'] }} ring-1">
                                                            {{ ucfirst($activity['action'] ?? 'activity') }}
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $activity['timestamp']->format('F d, Y') }} • {{ $activity['timestamp']->format('h:i A') }}
                                                        </span>
                                                    </div>

                                                    <p class="text-base font-semibold text-gray-900 mb-2">
                                                        {{ $activity['details'] }}
                                                    </p>

                                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                                                        <div class="flex items-center">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-600 mr-2">
                                                                <i class="fas fa-user text-xs"></i>
                                                            </span>
                                                            <div>
                                                                <div class="font-medium text-gray-800">{{ $activity['user']->name ?? 'System' }}</div>
                                                                <div class="text-xs text-gray-500">{{ $activity['user']->email ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center text-xs text-gray-500">
                                                            <i class="fas fa-clock mr-2"></i>
                                                            <span>{{ $activity['timestamp']->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-600">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                </div>
                                            </div>
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
