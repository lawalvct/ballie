@extends('layouts.super-admin')

@section('title', 'Backup Management')
@section('page-title', 'Backup Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Backup Management</h1>
                <p class="text-sm text-gray-600 mt-1">Create and manage server and local backups for ballie.co</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    <!-- Backup Options Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Server Backup Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900">Server Backup</h2>
                        <p class="text-sm text-gray-600">CyberPanel Server</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Full Website Backup</p>
                            <p class="text-xs text-gray-500">Files, databases, configurations</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Stored on Server</p>
                            <p class="text-xs text-gray-500">Managed by CyberPanel</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Quick Restore</p>
                            <p class="text-xs text-gray-500">One-click restoration</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-amber-800">
                            <strong>Website:</strong> ballie.co
                        </p>
                    </div>
                    <form method="POST" action="{{ route('super-admin.backups.create-server') }}" onsubmit="return confirm('Are you sure you want to create a server backup? This may take several minutes.');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Create Server Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Local Backup Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900">Local Backup</h2>
                        <p class="text-sm text-gray-600">Download to Computer</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Offline Storage</p>
                            <p class="text-xs text-gray-500">Keep backup on your computer</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Database Export</p>
                            <p class="text-xs text-gray-500">SQL dump of all databases</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Extra Security</p>
                            <p class="text-xs text-gray-500">Independent of server</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-blue-800">
                            <strong>Coming Soon:</strong> Local backup feature is under development
                        </p>
                    </div>
                    <form method="POST" action="{{ route('super-admin.backups.create-local') }}">
                        @csrf
                        <button type="submit" disabled class="w-full px-4 py-3 bg-gray-300 text-gray-500 rounded-lg font-medium cursor-not-allowed">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Create Local Backup (Coming Soon)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Information -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-md border border-indigo-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Backup Best Practices</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-indigo-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Regular Backups</p>
                    <p class="text-xs text-gray-600 mt-1">Create backups before major updates</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-purple-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Test Restores</p>
                    <p class="text-xs text-gray-600 mt-1">Verify backups can be restored</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-pink-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Multiple Copies</p>
                    <p class="text-xs text-gray-600 mt-1">Keep both server and local backups</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
