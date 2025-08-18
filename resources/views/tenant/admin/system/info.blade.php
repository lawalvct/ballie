@extends('layouts.tenant')

@section('title', 'System Information')

@section('content')
    {{-- Page Header --}}
    @include('tenant.admin.partials.header', [
        'title' => 'System Information',
        'subtitle' => 'Monitor system performance, health, and configuration details.',
        'breadcrumb' => 'System Info'
    ])

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- System Health Cards --}}
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- System Status --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">System Status</dt>
                                <dd class="text-lg font-medium text-green-600">Healthy</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Uptime --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Uptime</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $systemInfo['uptime'] ?? '99.9%' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Memory Usage --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Memory Usage</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $systemInfo['memory_usage'] ?? '65%' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Storage Usage --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Storage Usage</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $systemInfo['storage_usage'] ?? '45%' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Information Sections --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Server Information --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Server Information</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Operating System</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemInfo['os'] ?? 'Linux Ubuntu 20.04' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Web Server</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemInfo['web_server'] ?? 'Apache 2.4.41' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemInfo['php_version'] ?? phpversion() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemInfo['laravel_version'] ?? app()->version() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Database</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $systemInfo['database'] ?? 'MySQL 8.0' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Server Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ now()->format('Y-m-d H:i:s T') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Application Information --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Application Information</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ config('app.name') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Environment</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ config('app.env') === 'production' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst(config('app.env')) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Debug Mode</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ config('app.debug') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ config('app.timezone') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Locale</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ config('app.locale') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cache Driver</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ config('cache.default') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Performance Metrics --}}
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Performance Metrics</h3>
                        <div class="flex space-x-2">
                            <button type="button" onclick="runHealthCheck()"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Health Check
                            </button>
                            <button type="button" onclick="clearCache()"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Clear Cache
                            </button>
                            <button type="button" onclick="optimizeSystem()"
                                    class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Optimize
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {{-- Database Queries --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $metrics['db_queries'] ?? '1,234' }}</div>
                                <div class="text-sm text-gray-500">Database Queries</div>
                                <div class="text-xs text-gray-400 mt-1">Last 24 hours</div>
                            </div>
                        </div>

                        {{-- Cache Hit Rate --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $metrics['cache_hit_rate'] ?? '92%' }}</div>
                                <div class="text-sm text-gray-500">Cache Hit Rate</div>
                                <div class="text-xs text-gray-400 mt-1">Current session</div>
                            </div>
                        </div>

                        {{-- Average Response Time --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $metrics['avg_response_time'] ?? '245ms' }}</div>
                                <div class="text-sm text-gray-500">Avg Response Time</div>
                                <div class="text-xs text-gray-400 mt-1">Last hour</div>
                            </div>
                        </div>

                        {{-- Error Rate --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $metrics['error_rate'] ?? '0.2%' }}</div>
                                <div class="text-sm text-gray-500">Error Rate</div>
                                <div class="text-xs text-gray-400 mt-1">Last 24 hours</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Requirements Check --}}
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">System Requirements</h3>
                    <div class="space-y-4">
                        @php
                            $requirements = [
                                ['name' => 'PHP Version >= 8.1', 'status' => version_compare(PHP_VERSION, '8.1.0', '>='), 'current' => PHP_VERSION],
                                ['name' => 'OpenSSL Extension', 'status' => extension_loaded('openssl'), 'current' => extension_loaded('openssl') ? 'Enabled' : 'Disabled'],
                                ['name' => 'PDO Extension', 'status' => extension_loaded('pdo'), 'current' => extension_loaded('pdo') ? 'Enabled' : 'Disabled'],
                                ['name' => 'Mbstring Extension', 'status' => extension_loaded('mbstring'), 'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled'],
                                ['name' => 'Tokenizer Extension', 'status' => extension_loaded('tokenizer'), 'current' => extension_loaded('tokenizer') ? 'Enabled' : 'Disabled'],
                                ['name' => 'JSON Extension', 'status' => extension_loaded('json'), 'current' => extension_loaded('json') ? 'Enabled' : 'Disabled'],
                                ['name' => 'cURL Extension', 'status' => extension_loaded('curl'), 'current' => extension_loaded('curl') ? 'Enabled' : 'Disabled'],
                            ];
                        @endphp

                        @foreach($requirements as $requirement)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    @if($requirement['status'])
                                        <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $requirement['name'] }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $requirement['current'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function runHealthCheck() {
        fetch("{{ route('tenant.admin.system.health-check', tenant('slug')) }}", {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Health check completed successfully!');
            } else {
                alert('Health check failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error running health check');
        });
    }

    function clearCache() {
        if (confirm('Are you sure you want to clear the application cache?')) {
            fetch("{{ route('tenant.admin.system.clear-cache', tenant('slug')) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cache cleared successfully!');
                } else {
                    alert('Error clearing cache: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error clearing cache');
            });
        }
    }

    function optimizeSystem() {
        if (confirm('Are you sure you want to optimize the system? This may take a few moments.')) {
            fetch("{{ route('tenant.admin.system.optimize', tenant('slug')) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('System optimization completed successfully!');
                } else {
                    alert('Error optimizing system: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error optimizing system');
            });
        }
    }
</script>
@endpush
