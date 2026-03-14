@extends('layouts.tenant')

@section('title', 'Active Projects Report')
@section('page-title', 'Active Projects Report')
@section('page-description')
    <span class="hidden md:inline">Overview of all currently active projects, progress, and deadlines.</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.projects.reports.completed', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Completed Projects
            </a>
            <a href="{{ route('tenant.projects.reports.profitability', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Profitability
            </a>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.projects.reports', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Sort Tabs -->
    <div class="bg-white rounded-2xl p-4 shadow-lg">
        <div class="flex flex-wrap gap-2">
            @php
                $sorts = [
                    'end_date' => 'By Deadline',
                    'progress' => 'By Progress',
                    'budget' => 'By Budget',
                    'priority' => 'By Priority',
                ];
            @endphp
            @foreach($sorts as $key => $label)
                <a href="{{ route('tenant.projects.reports.active', ['tenant' => $tenant->slug, 'sort' => $key]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $sortBy === $key ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Active Projects</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $summary['total_active'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-red-500">
            <p class="text-sm font-medium text-gray-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $summary['overdue'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-violet-500">
            <p class="text-sm font-medium text-gray-500">Total Budget</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_budget'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Total Spent</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_spent'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Avg Progress</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $summary['avg_progress'] }}%</p>
        </div>
    </div>

    <!-- Active Projects Cards -->
    @if($projectData->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($projectData as $project)
                <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow {{ $project->is_overdue ? 'border-l-4 border-red-500' : '' }}">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}" class="text-lg font-semibold text-violet-600 hover:text-violet-800">
                                {{ $project->name }}
                            </a>
                            <p class="text-xs text-gray-400 font-mono">{{ $project->project_number }}</p>
                        </div>
                        @php
                            $prioColors = ['low' => 'bg-gray-100 text-gray-600', 'medium' => 'bg-blue-100 text-blue-700', 'high' => 'bg-orange-100 text-orange-700', 'urgent' => 'bg-red-100 text-red-700'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $prioColors[$project->priority] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </div>

                    <!-- Client & Assignee -->
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                        @if($project->customer)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $project->customer->company_name ?: $project->customer->first_name . ' ' . $project->customer->last_name }}
                            </div>
                        @endif
                        @if($project->assigned_user)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $project->assigned_user->name }}
                            </div>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-500">Tasks: {{ $project->done_tasks }}/{{ $project->total_tasks }}</span>
                            <span class="text-xs font-semibold text-gray-700">{{ $project->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full transition-all duration-500 {{ $project->progress >= 75 ? 'bg-green-500' : ($project->progress >= 50 ? 'bg-blue-500' : ($project->progress >= 25 ? 'bg-yellow-500' : 'bg-red-500')) }}"
                                 style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>

                    <!-- Budget & Dates -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Budget</p>
                            <p class="font-semibold text-gray-900">{{ $project->budget ? '₦' . number_format($project->budget, 2) : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Spent</p>
                            <p class="font-semibold {{ $project->budget > 0 && $project->actual_cost > $project->budget ? 'text-red-600' : 'text-gray-900' }}">
                                ₦{{ number_format($project->actual_cost, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Deadline</p>
                            <p class="font-semibold {{ $project->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $project->end_date ? $project->end_date->format('M d, Y') : '—' }}
                                @if($project->is_overdue)
                                    <span class="text-xs">({{ abs($project->days_remaining) }}d overdue)</span>
                                @elseif($project->days_remaining !== null && $project->days_remaining >= 0)
                                    <span class="text-xs text-gray-400">({{ $project->days_remaining }}d left)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Revenue</p>
                            <p class="font-semibold text-green-600">₦{{ number_format($project->revenue, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>No active projects at the moment.</p>
        </div>
    @endif
</div>
@endsection
