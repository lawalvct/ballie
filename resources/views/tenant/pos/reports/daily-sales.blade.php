@extends('layouts.tenant')

@section('title', 'Daily Sales Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Daily Sales Report</h1>
        <p class="text-gray-600">Daily sales performance and analytics</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Daily Sales Report</h3>
            <p class="mt-1 text-sm text-gray-500">This report is under development</p>
            <div class="mt-6">
                <a href="{{ route('tenant.pos.reports', $tenant->slug) }}" class="text-blue-600 hover:text-blue-800">← Back to Reports</a>
            </div>
        </div>
    </div>
</div>
@endsection
