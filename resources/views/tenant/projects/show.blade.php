@extends('layouts.tenant')

@section('title', $project->name)
@section('page-title', $project->name)
@section('page-description')
    <span class="hidden md:inline">
        {{ $project->project_number }} &middot; {{ ucfirst(str_replace('_', ' ', $project->status)) }}
    </span>
@endsection

@section('content')
<div x-data="projectShow()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.projects.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Projects
            </a>
        </div>
        <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest shadow-sm transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Update Status
                    <svg class="w-4 h-4 ml-1.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-100 z-20 overflow-hidden">
                    @foreach(['draft' => ['label' => 'Draft', 'color' => 'text-gray-600', 'dot' => 'bg-gray-400'], 'active' => ['label' => 'Active', 'color' => 'text-green-700', 'dot' => 'bg-green-500'], 'on_hold' => ['label' => 'On Hold', 'color' => 'text-yellow-700', 'dot' => 'bg-yellow-500'], 'completed' => ['label' => 'Completed', 'color' => 'text-blue-700', 'dot' => 'bg-blue-500'], 'archived' => ['label' => 'Archived', 'color' => 'text-gray-500', 'dot' => 'bg-gray-400']] as $value => $meta)
                    @if($value !== $project->status)
                    <form action="{{ route('tenant.projects.status.update', [$tenant->slug, $project->id]) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $value }}">
                        <button type="submit"
                                class="w-full flex items-center px-4 py-2.5 text-sm {{ $meta['color'] }} hover:bg-gray-50 transition-colors">
                            <span class="w-2 h-2 rounded-full {{ $meta['dot'] }} mr-2.5 flex-shrink-0"></span>
                            {{ $meta['label'] }}
                        </button>
                    </form>
                    @endif
                    @endforeach
                </div>
            </div>
            <a href="{{ route('tenant.projects.edit', [$tenant->slug, $project->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
        <div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div></div>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
        <div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="ml-3"><p class="text-sm font-medium text-red-800">{{ session('error') }}</p></div></div>
    </div>
    @endif

    <!-- Project Overview Cards -->
    @php
        $statusColors = [
            'draft' => 'bg-gray-100 text-gray-700 border-gray-300',
            'active' => 'bg-green-100 text-green-700 border-green-300',
            'on_hold' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'completed' => 'bg-blue-100 text-blue-700 border-blue-300',
            'archived' => 'bg-gray-100 text-gray-500 border-gray-300',
        ];
        $priorityColors = [
            'low' => 'bg-gray-100 text-gray-600',
            'medium' => 'bg-blue-100 text-blue-600',
            'high' => 'bg-orange-100 text-orange-600',
            'urgent' => 'bg-red-100 text-red-600',
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status & Priority -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Status & Priority</p>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusColors[$project->status] ?? '' }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$project->priority] ?? '' }}">
                    {{ ucfirst($project->priority) }}
                </span>
            </div>
        </div>

        <!-- Progress -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Task Progress</p>
            <div class="flex items-center space-x-3">
                <div class="flex-1">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php $progress = $taskStats['total'] > 0 ? round(($taskStats['done'] / $taskStats['total']) * 100) : 0; @endphp
                        <div class="h-2.5 rounded-full {{ $progress >= 100 ? 'bg-green-500' : 'bg-violet-500' }}" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                <span class="text-lg font-semibold text-gray-900">{{ $progress }}%</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $taskStats['done'] }} of {{ $taskStats['total'] }} tasks done</p>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Timeline</p>
            @if($project->start_date || $project->end_date)
                <p class="text-sm font-medium text-gray-900">
                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '—' }}
                </p>
                <p class="text-xs text-gray-500">
                    to {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : '—' }}
                    @if($project->is_overdue)
                        <span class="text-red-500 font-medium">(Overdue)</span>
                    @elseif($project->days_remaining !== null && $project->days_remaining >= 0)
                        <span>({{ $project->days_remaining }} days left)</span>
                    @endif
                </p>
            @else
                <p class="text-sm text-gray-400">No dates set</p>
            @endif
        </div>

        <!-- Budget -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Budget</p>
            @if($project->budget)
                <p class="text-lg font-semibold text-gray-900">₦{{ number_format($project->budget, 2) }}</p>
                @if($project->actual_cost)
                    <p id="budget-spent-summary" class="text-xs text-gray-500">
                        Spent: ₦<span id="budget-spent-amount">{{ number_format($project->actual_cost, 2) }}</span>
                        (<span id="budget-used-percent">{{ $project->budget_used_percent }}</span>%)
                    </p>
                @endif
            @else
                <p class="text-sm text-gray-400">No budget set</p>
            @endif
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Overview
                </button>
                <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Tasks <span class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $taskStats['total'] }}</span>
                </button>
                <button @click="activeTab = 'milestones'" :class="activeTab === 'milestones' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Milestones <span id="milestones-count-badge" class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $milestoneStats['total'] }}</span>
                </button>
                <button @click="activeTab = 'notes'" :class="activeTab === 'notes' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Notes <span id="notes-count-badge" class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->notes->count() }}</span>
                </button>
                <button @click="activeTab = 'files'" :class="activeTab === 'files' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Files <span class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->attachments->count() }}</span>
                </button>
                <button @click="activeTab = 'expenses'" :class="activeTab === 'expenses' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Expenses <span id="expenses-count-badge" class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->expenses->count() }}</span>
                </button>
            </nav>
        </div>

        <div class="p-6">
            @include('tenant.projects.partials._overview')
            @include('tenant.projects.partials._tasks')
            @include('tenant.projects.partials._milestones')
            @include('tenant.projects.partials._notes')
            @include('tenant.projects.partials._files')
            @include('tenant.projects.partials._expenses')
        </div>
    </div>
</div>
@endsection

@include('tenant.projects.partials._scripts')
