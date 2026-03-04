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
                    <p class="text-xs text-gray-500">
                        Spent: ₦{{ number_format($project->actual_cost, 2) }}
                        ({{ $project->budget_used_percent }}%)
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
                    Milestones <span class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $milestoneStats['total'] }}</span>
                </button>
                <button @click="activeTab = 'notes'" :class="activeTab === 'notes' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Notes <span class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->notes->count() }}</span>
                </button>
                <button @click="activeTab = 'files'" :class="activeTab === 'files' ? 'border-violet-500 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Files <span class="ml-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->attachments->count() }}</span>
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- ═══════════ OVERVIEW TAB ═══════════ -->
            <div x-show="activeTab === 'overview'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Details -->
                    <div class="lg:col-span-2 space-y-6">
                        @if($project->description)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                                <p class="text-gray-700 whitespace-pre-line">{{ $project->description }}</p>
                            </div>
                        @endif

                        <!-- Task Summary -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Task Breakdown</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-gray-700">{{ $taskStats['todo'] }}</p>
                                    <p class="text-xs text-gray-500">To Do</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-blue-600">{{ $taskStats['in_progress'] }}</p>
                                    <p class="text-xs text-gray-500">In Progress</p>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-yellow-600">{{ $taskStats['review'] }}</p>
                                    <p class="text-xs text-gray-500">Review</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $taskStats['done'] }}</p>
                                    <p class="text-xs text-gray-500">Done</p>
                                </div>
                                <div class="bg-red-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-red-600">{{ $taskStats['overdue'] }}</p>
                                    <p class="text-xs text-gray-500">Overdue</p>
                                </div>
                            </div>
                        </div>

                        <!-- Milestone Summary -->
                        @if($milestoneStats['total'] > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Milestone Summary</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-gray-700">{{ $milestoneStats['total'] }}</p>
                                    <p class="text-xs text-gray-500">Total</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $milestoneStats['completed'] }}</p>
                                    <p class="text-xs text-gray-500">Completed</p>
                                </div>
                                <div class="bg-violet-50 rounded-lg p-3 text-center">
                                    <p class="text-lg font-bold text-violet-600">₦{{ number_format($milestoneStats['billable_total'], 0) }}</p>
                                    <p class="text-xs text-gray-500">Billable Total</p>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-3 text-center">
                                    <p class="text-lg font-bold text-orange-600">₦{{ number_format($milestoneStats['unbilled_total'], 0) }}</p>
                                    <p class="text-xs text-gray-500">Unbilled</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Right Column: Sidebar Info -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Project Number</p>
                                <p class="text-sm font-mono text-gray-900">{{ $project->project_number }}</p>
                            </div>

                            @if($project->customer)
                            <div>
                                <p class="text-xs font-medium text-gray-500">Client</p>
                                <p class="text-sm text-gray-900">{{ $project->customer->first_name }} {{ $project->customer->last_name }}</p>
                                @if($project->customer->company_name)
                                    <p class="text-xs text-gray-500">{{ $project->customer->company_name }}</p>
                                @endif
                            </div>
                            @endif

                            @if($project->assignedUser)
                            <div>
                                <p class="text-xs font-medium text-gray-500">Project Manager</p>
                                <p class="text-sm text-gray-900">{{ $project->assignedUser->name }}</p>
                            </div>
                            @endif

                            @if($project->creator)
                            <div>
                                <p class="text-xs font-medium text-gray-500">Created By</p>
                                <p class="text-sm text-gray-900">{{ $project->creator->name }}</p>
                                <p class="text-xs text-gray-500">{{ $project->created_at->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════ TASKS TAB ═══════════ -->
            <div x-show="activeTab === 'tasks'" x-transition>
                <!-- Add Task Form -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Add New Task</h4>
                    <form @submit.prevent="addTask()" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-4">
                            <input type="text" x-model="newTask.title" placeholder="Task title..." required
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <div class="md:col-span-2">
                            <select x-model="newTask.priority" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <select x-model="newTask.assigned_to" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                                <option value="">Unassigned</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <input type="date" x-model="newTask.due_date" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" :disabled="taskLoading"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                                <svg x-show="taskLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Add Task
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Task List -->
                <div class="space-y-2">
                    @forelse($project->tasks as $task)
                        @php
                            $taskStatusColors = [
                                'todo' => 'border-l-gray-400',
                                'in_progress' => 'border-l-blue-500',
                                'review' => 'border-l-yellow-500',
                                'done' => 'border-l-green-500',
                            ];
                            $taskPriorityBadge = [
                                'low' => 'bg-gray-100 text-gray-600',
                                'medium' => 'bg-blue-100 text-blue-600',
                                'high' => 'bg-orange-100 text-orange-600',
                                'urgent' => 'bg-red-100 text-red-600',
                            ];
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 border-l-4 {{ $taskStatusColors[$task->status] ?? 'border-l-gray-300' }} rounded-lg hover:bg-gray-50 transition-colors duration-200"
                             id="task-{{ $task->id }}">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <!-- Status Dropdown -->
                                <select onchange="updateTaskStatus({{ $task->id }}, this.value)"
                                        class="border-gray-300 rounded text-xs focus:ring-violet-500 focus:border-violet-500 py-1">
                                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>Review</option>
                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                </select>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }} truncate">
                                        {{ $task->title }}
                                    </p>
                                    <div class="flex items-center space-x-3 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $taskPriorityBadge[$task->priority] ?? '' }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        @if($task->assignedUser)
                                            <span class="text-xs text-gray-500">{{ $task->assignedUser->name }}</span>
                                        @endif
                                        @if($task->due_date)
                                            <span class="text-xs {{ $task->is_overdue ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                                Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button onclick="deleteTask({{ $task->id }})" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete task">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <p>No tasks yet. Add your first task above.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ═══════════ MILESTONES TAB ═══════════ -->
            <div x-show="activeTab === 'milestones'" x-transition>
                <!-- Add Milestone Form -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Add New Milestone</h4>
                    <form @submit.prevent="addMilestone()" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-4">
                            <input type="text" x-model="newMilestone.title" placeholder="Milestone title..." required
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <div class="md:col-span-2">
                            <input type="number" x-model="newMilestone.amount" placeholder="Amount (₦)" step="0.01" min="0"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <div class="md:col-span-2">
                            <input type="date" x-model="newMilestone.due_date"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center h-full px-2">
                                <input type="checkbox" x-model="newMilestone.is_billable" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="ml-2 text-sm text-gray-700">Billable</span>
                            </label>
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" :disabled="milestoneLoading"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                                Add Milestone
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Milestones List -->
                <div class="space-y-3">
                    @forelse($project->milestones as $milestone)
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg" id="milestone-{{ $milestone->id }}">
                            <div class="flex items-center space-x-4 flex-1 min-w-0">
                                <!-- Complete Toggle -->
                                <button onclick="toggleMilestone({{ $milestone->id }}, {{ $milestone->completed_at ? 'false' : 'true' }})"
                                        class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors duration-200 {{ $milestone->completed_at ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-green-400' }}">
                                    @if($milestone->completed_at)
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </button>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 {{ $milestone->completed_at ? 'line-through text-gray-400' : '' }}">
                                        {{ $milestone->title }}
                                    </p>
                                    <div class="flex items-center space-x-3 mt-1">
                                        @if($milestone->amount)
                                            <span class="text-xs font-medium {{ $milestone->is_billable ? 'text-violet-600' : 'text-gray-500' }}">
                                                ₦{{ number_format($milestone->amount, 2) }}
                                                @if($milestone->is_billable) (Billable) @endif
                                            </span>
                                        @endif
                                        @if($milestone->due_date)
                                            <span class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}</span>
                                        @endif
                                        @if($milestone->invoice_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Invoiced</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button onclick="deleteMilestone({{ $milestone->id }})" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete milestone">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                            <p>No milestones yet. Add one above to track project deliverables.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ═══════════ NOTES TAB ═══════════ -->
            <div x-show="activeTab === 'notes'" x-transition>
                <!-- Add Note Form -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Add Note</h4>
                    <form @submit.prevent="addNote()">
                        <textarea x-model="newNote.content" rows="3" placeholder="Write a note about this project..." required
                                  class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500 mb-3"></textarea>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newNote.is_internal" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                <span class="ml-2 text-sm text-gray-600">Internal note (not visible to client)</span>
                            </label>
                            <button type="submit" :disabled="noteLoading"
                                    class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                                Add Note
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notes Feed -->
                <div class="space-y-4">
                    @forelse($project->notes as $note)
                        <div class="flex space-x-3" id="note-{{ $note->id }}">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-violet-600">{{ strtoupper(substr($note->user->name ?? '?', 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 bg-white rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $note->user->name ?? 'Unknown' }}</span>
                                        <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                                        @if($note->is_internal)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Internal</span>
                                        @endif
                                    </div>
                                    <button onclick="deleteNote({{ $note->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete note">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $note->content }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            <p>No notes yet. Add one to keep track of important information.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ═══════════ FILES TAB ═══════════ -->
            <div x-show="activeTab === 'files'" x-transition>
                <!-- Upload Form -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Upload File</h4>
                    <form @submit.prevent="uploadFile()" enctype="multipart/form-data">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <input type="file" x-ref="fileInput" @change="selectedFile = $event.target.files[0]"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                <p class="mt-1 text-xs text-gray-500">Max file size: 10MB</p>
                            </div>
                            <button type="submit" :disabled="fileLoading || !selectedFile"
                                    class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                                <svg x-show="fileLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Files List -->
                <div class="space-y-2">
                    @forelse($project->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200" id="attachment-{{ $attachment->id }}">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <!-- File Icon -->
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center {{ $attachment->is_image ? 'bg-green-100' : 'bg-gray-100' }}">
                                    @if($attachment->is_image)
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->file_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $attachment->file_size_formatted }} &middot; {{ $attachment->user->name ?? 'Unknown' }} &middot; {{ $attachment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 ml-3">
                                <a href="{{ route('tenant.projects.attachments.download', [$tenant->slug, $project->id, $attachment->id]) }}"
                                   class="p-1 text-gray-400 hover:text-violet-600 transition-colors duration-200" title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                                <button onclick="deleteAttachment({{ $attachment->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            <p>No files uploaded yet. Add project documents, images, or contracts above.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const PROJECT_ID = {{ $project->id }};
    const TENANT_SLUG = '{{ $tenant->slug }}';
    const BASE_URL = `/{{ $tenant->slug }}/projects/${PROJECT_ID}`;
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

    function projectShow() {
        return {
            activeTab: '{{ $tab }}',
            // Task
            newTask: { title: '', priority: 'medium', assigned_to: '', due_date: '' },
            taskLoading: false,
            // Milestone
            newMilestone: { title: '', amount: '', due_date: '', is_billable: true },
            milestoneLoading: false,
            // Note
            newNote: { content: '', is_internal: true },
            noteLoading: false,
            // File
            selectedFile: null,
            fileLoading: false,

            async addTask() {
                this.taskLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/tasks`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newTask)
                    });
                    if (res.ok) {
                        this.newTask = { title: '', priority: 'medium', assigned_to: '', due_date: '' };
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Failed to add task');
                    }
                } catch (e) { alert('Error adding task'); }
                this.taskLoading = false;
            },

            async addMilestone() {
                this.milestoneLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/milestones`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newMilestone)
                    });
                    if (res.ok) {
                        this.newMilestone = { title: '', amount: '', due_date: '', is_billable: true };
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Failed to add milestone');
                    }
                } catch (e) { alert('Error adding milestone'); }
                this.milestoneLoading = false;
            },

            async addNote() {
                this.noteLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/notes`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newNote)
                    });
                    if (res.ok) {
                        this.newNote = { content: '', is_internal: true };
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Failed to add note');
                    }
                } catch (e) { alert('Error adding note'); }
                this.noteLoading = false;
            },

            async uploadFile() {
                if (!this.selectedFile) return;
                this.fileLoading = true;
                try {
                    const formData = new FormData();
                    formData.append('file', this.selectedFile);
                    const res = await fetch(`${BASE_URL}/attachments`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: formData
                    });
                    if (res.ok) {
                        this.selectedFile = null;
                        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Failed to upload file');
                    }
                } catch (e) { alert('Error uploading file'); }
                this.fileLoading = false;
            }
        };
    }

    async function updateTaskStatus(taskId, status) {
        try {
            const res = await fetch(`${BASE_URL}/tasks/${taskId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            if (res.ok) window.location.reload();
        } catch (e) { alert('Error updating task'); }
    }

    async function deleteTask(taskId) {
        if (!confirm('Delete this task?')) return;
        try {
            const res = await fetch(`${BASE_URL}/tasks/${taskId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`task-${taskId}`);
                if (el) el.remove();
            }
        } catch (e) { alert('Error deleting task'); }
    }

    async function toggleMilestone(milestoneId, complete) {
        const body = complete ? { mark_complete: true } : { mark_incomplete: true };
        try {
            const res = await fetch(`${BASE_URL}/milestones/${milestoneId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify(body)
            });
            if (res.ok) window.location.reload();
        } catch (e) { alert('Error updating milestone'); }
    }

    async function deleteMilestone(milestoneId) {
        if (!confirm('Delete this milestone?')) return;
        try {
            const res = await fetch(`${BASE_URL}/milestones/${milestoneId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`milestone-${milestoneId}`);
                if (el) el.remove();
            }
        } catch (e) { alert('Error deleting milestone'); }
    }

    async function deleteNote(noteId) {
        if (!confirm('Delete this note?')) return;
        try {
            const res = await fetch(`${BASE_URL}/notes/${noteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`note-${noteId}`);
                if (el) el.remove();
            }
        } catch (e) { alert('Error deleting note'); }
    }

    async function deleteAttachment(attachmentId) {
        if (!confirm('Delete this file?')) return;
        try {
            const res = await fetch(`${BASE_URL}/attachments/${attachmentId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`attachment-${attachmentId}`);
                if (el) el.remove();
            }
        } catch (e) { alert('Error deleting file'); }
    }
</script>
@endpush
