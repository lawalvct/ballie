@extends('layouts.tenant')

@section('title', 'Top Products Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Top Products Report</h1>
        <p class="text-gray-600">Best selling products analysis</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Top Products Report</h3>
            <p class="mt-1 text-sm text-gray-500">This report is under development</p>
            <div class="mt-6">
                <a href="{{ route('tenant.pos.reports', $tenant->slug) }}" class="text-blue-600 hover:text-blue-800">← Back to Reports</a>
            </div>
        </div>
    </div>
</div>
@endsection
