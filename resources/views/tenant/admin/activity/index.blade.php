@extends('layouts.tenant')

@section('title', 'Activity Logs')

@section('content')
    {{-- Page Header --}}
    @include('tenant.admin.partials.header', [
        'title' => 'Activity Logs',
        'subtitle' => 'Monitor user activities and system events across your organization.',
        'breadcrumb' => 'Activity'
    ])

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Real-time Status & Quick Actions --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-2"></div>
                    <span class="text-sm text-gray-600">Live monitoring active</span>
                </div>
                <label class="flex items-center">
                    <input type="checkbox" name="auto_refresh" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                    <span class="ml-2 text-sm text-gray-600">Auto-refresh (30s)</span>
                </label>
            </div>
            <div class="mt-4 sm:mt-0 text-sm text-gray-500">
                Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>

        {{-- Activity Statistics --}}
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Activities --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Activities</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_activities'] ?? 0) }}</dd>
                                <dd class="text-xs text-gray-400 mt-1">All time</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Today's Activities --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Activities</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_activities'] ?? 0) }}</dd>
                                <dd class="text-xs text-green-600 mt-1">{{ now()->format('M j, Y') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Users --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users'] ?? 0) }}</dd>
                                <dd class="text-xs text-purple-600 mt-1">Last 24 hours</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Critical Events --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow {{ ($stats['critical_events'] ?? 0) > 0 ? 'ring-2 ring-red-200' : '' }}">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center {{ ($stats['critical_events'] ?? 0) > 0 ? 'animate-pulse' : '' }}">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Critical Events</dt>
                                <dd class="text-2xl font-bold {{ ($stats['critical_events'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($stats['critical_events'] ?? 0) }}</dd>
                                <dd class="text-xs {{ ($stats['critical_events'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-400' }} mt-1">Requires attention</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Logs Table --}}
        @component('tenant.admin.partials.table', [
            'showHeader' => true,
            'tableTitle' => 'Activity Logs',
            'tableSubtitle' => 'Track all user activities and system events.',
            'showFilters' => true,
            'showBulkActions' => true,
            'showActions' => true,
            'columns' => [
                ['label' => 'User', 'sortable' => true],
                ['label' => 'Activity', 'sortable' => true],
                ['label' => 'Module', 'sortable' => true],
                ['label' => 'IP Address', 'sortable' => false],
                ['label' => 'Date/Time', 'sortable' => true]
            ]
        ])
            @slot('headerActions')
                <button type="button" onclick="exportLogs()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </button>
                <button type="button" onclick="clearOldLogs()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Clean Old Logs
                </button>
            @endslot

            @slot('filters')
                <div class="flex flex-wrap gap-3">
                    <select name="user_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" onchange="applyFilters()">
                        <option value="">All Users</option>
                        <option value="admin">Admins Only</option>
                        <option value="manager">Managers Only</option>
                        <option value="user">Regular Users</option>
                        <option value="system">System Events</option>
                    </select>
                    <select name="activity_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" onchange="applyFilters()">
                        <option value="">All Activities</option>
                        <option value="login">Logins</option>
                        <option value="logout">Logouts</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="security">Security Events</option>
                    </select>
                    <select name="time_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" onchange="applyFilters()">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <input type="text" name="search" placeholder="Search activities..." 
                           class="border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" 
                           onkeyup="debounceSearch(this.value)">
                    <button type="button" onclick="clearFilters()" 
                            class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                        Clear
                    </button>
                </div>
            @endslot

            {{-- Table Rows --}}
            @forelse($activities ?? [] as $activity)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" value="{{ $activity->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($activity->user && $activity->user->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $activity->user->avatar }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full {{ $activity->user ? 'bg-gradient-to-r from-purple-400 to-pink-400' : 'bg-gradient-to-r from-gray-400 to-gray-500' }} flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr($activity->user->first_name ?? 'S', 0, 1) }}{{ substr($activity->user->last_name ?? 'Y', 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $activity->user->first_name ?? 'System' }} {{ $activity->user->last_name ?? '' }}
                                    @if($activity->user && $activity->user->is_online)
                                        <span class="ml-2 inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $activity->user->email ?? 'system@system.com' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-start space-x-2">
                            <div class="flex-shrink-0 mt-1">
                                @php
                                    $eventIcon = match($activity->event ?? 'default') {
                                        'login' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
                                        'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
                                        'created' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                        'updated' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                        'deleted' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                                        default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                                    };
                                    $eventColor = match($activity->log_name ?? 'default') {
                                        'security' => 'text-red-500',
                                        'admin' => 'text-purple-500',
                                        'user' => 'text-blue-500',
                                        default => 'text-gray-400'
                                    };
                                @endphp
                                <svg class="w-4 h-4 {{ $eventColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $eventIcon }}" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $activity->description ?? $activity->event }}</div>
                                @if($activity->subject_type && $activity->subject_id)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ class_basename($activity->subject_type) }} ID: {{ $activity->subject_id }}
                                    </div>
                                @endif
                                @if($activity->properties && count($activity->properties) > 0)
                                    <div class="text-sm text-gray-500 mt-2">
                                        @foreach(array_slice($activity->properties, 0, 3) as $key => $value)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                {{ $key }}: {{ is_array($value) ? 'Array' : Str::limit($value, 20) }}
                                            </span>
                                        @endforeach
                                        @if(count($activity->properties) > 3)
                                            <span class="text-xs text-gray-400">+{{ count($activity->properties) - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $activity->log_name === 'security' ? 'bg-red-100 text-red-800' : ($activity->log_name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($activity->log_name ?? 'default') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $activity->ip_address ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-900">{{ $activity->created_at->format('M j, Y') }}</span>
                            <span class="text-xs text-gray-500">{{ $activity->created_at->format('H:i:s') }}</span>
                            <span class="text-xs text-gray-400 mt-1" title="{{ $activity->created_at->toDateTimeString() }}">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('tenant.admin.activity.show', [tenant('slug'), $activity]) }}"
                               class="text-purple-600 hover:text-purple-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            @if($activity->user_id)
                                <a href="{{ route('tenant.admin.activity.user', [tenant('slug'), $activity->user]) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No activity logs found</h3>
                            <p class="mt-1 text-sm text-gray-500">Activity logs will appear here as users interact with the system.</p>
                        </div>
                    </td>
                </tr>
            @endforelse

            @slot('pagination')
                {{ $activities->links() ?? '' }}
            @endslot
        @endcomponent
    </div>
@endsection

@push('scripts')
<script>
    let searchTimeout;
    
    function exportLogs() {
        const params = new URLSearchParams({
            user_filter: document.querySelector('[name="user_filter"]').value,
            activity_filter: document.querySelector('[name="activity_filter"]').value,
            time_filter: document.querySelector('[name="time_filter"]').value,
            search: document.querySelector('[name="search"]').value
        });
        window.location.href = "{{ route('tenant.admin.activity.export', tenant('slug')) }}?" + params.toString();
    }

    function clearOldLogs() {
        if (confirm('Are you sure you want to clear old activity logs? This action cannot be undone.')) {
            const button = event.target;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Clearing...';
            
            fetch("{{ route('tenant.admin.activity.clear-old', tenant('slug')) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Old activity logs cleared successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error clearing logs: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error clearing logs', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>Clean Old Logs';
            });
        }
    }

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);
        params.set('user_filter', document.querySelector('[name="user_filter"]').value);
        params.set('activity_filter', document.querySelector('[name="activity_filter"]').value);
        params.set('time_filter', document.querySelector('[name="time_filter"]').value);
        params.set('search', document.querySelector('[name="search"]').value);
        window.location.search = params.toString();
    }

    function clearFilters() {
        document.querySelector('[name="user_filter"]').value = '';
        document.querySelector('[name="activity_filter"]').value = '';
        document.querySelector('[name="time_filter"]').value = '';
        document.querySelector('[name="search"]').value = '';
        window.location.href = window.location.pathname;
    }

    function debounceSearch(value) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (value.length >= 3 || value.length === 0) {
                applyFilters();
            }
        }, 500);
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function updateLastUpdated() {
        document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
    }

    // Auto-refresh every 30 seconds
    setInterval(function() {
        if (document.querySelector('[name="auto_refresh"]:checked')) {
            updateLastUpdated();
            location.reload();
        }
    }, 30000);

    // Initialize filters from URL params
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('user_filter')) document.querySelector('[name="user_filter"]').value = params.get('user_filter');
        if (params.get('activity_filter')) document.querySelector('[name="activity_filter"]').value = params.get('activity_filter');
        if (params.get('time_filter')) document.querySelector('[name="time_filter"]').value = params.get('time_filter');
        if (params.get('search')) document.querySelector('[name="search"]').value = params.get('search');
    });
</script>
@endpush
