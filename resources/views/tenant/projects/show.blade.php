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
            <!-- ═══════════ OVERVIEW TAB ═══════════ -->
            <div x-show="activeTab === 'overview'" x-transition>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Details -->
                    <div class="lg:col-span-2 space-y-6">
                        @if($project->description)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                                @php
                                    $cleanDescription = clean($project->description);
                                    $descriptionPreview = \Illuminate\Support\Str::words(strip_tags($cleanDescription), 40, '...');
                                    $hasLongDescription = trim(strip_tags($cleanDescription)) !== trim($descriptionPreview);
                                @endphp
                                <div x-data="{ expanded: false }" class="space-y-2">
                                    <div x-show="!expanded" class="text-sm text-gray-700 leading-6">
                                        {{ $descriptionPreview }}
                                    </div>
                                    <div x-show="expanded" x-transition class="text-gray-700 prose prose-sm max-w-none">{!! $cleanDescription !!}</div>
                                    @if($hasLongDescription)
                                        <button @click="expanded = !expanded" type="button" class="text-sm font-medium text-violet-600 hover:text-violet-700 transition-colors duration-200">
                                            <span x-show="!expanded">Read more</span>
                                            <span x-show="expanded">Show less</span>
                                        </button>
                                    @endif
                                </div>
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
                            <input type="text" x-ref="milestoneAmountInput" :value="milestoneAmountDisplay" @input="setMilestoneAmount($event.target.value)" inputmode="decimal" placeholder="Amount (₦)"
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
                <div id="milestones-list" class="space-y-3">
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

                            <div class="flex items-center space-x-2 ml-3">
                                @if($milestone->completed_at && $milestone->is_billable && $milestone->amount && !$milestone->invoice_id)
                                    <button onclick="invoiceMilestone({{ $milestone->id }})"
                                            class="inline-flex items-center px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 border border-green-200 transition" title="Create Invoice">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                                        </svg>
                                        Invoice
                                    </button>
                                @endif
                                <button onclick="deleteMilestone({{ $milestone->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete milestone">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div id="milestones-empty-state" class="text-center py-8 text-gray-400">
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
                <div id="notes-list" class="space-y-4">
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
                        <div id="notes-empty-state" class="text-center py-8 text-gray-400">
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

            <!-- ═══════════ EXPENSES TAB ═══════════ -->
            <div x-show="activeTab === 'expenses'" x-transition>
                <!-- Add Expense Form -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Record Project Expense</h4>
                        <form @submit.prevent="addExpense()" class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="md:col-span-4">
                            <input type="text" x-model="newExpense.title" placeholder="Expense title..." required
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                                </div>
                                <div class="md:col-span-2">
                                <input type="text" x-ref="expenseAmountInput" :value="expenseAmountDisplay" @input="setExpenseAmount($event.target.value)" inputmode="decimal" placeholder="Amount (₦)" required
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                                </div>
                                <div class="md:col-span-3">
                            <input type="date" x-model="newExpense.expense_date" required
                                   class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                                </div>
                                <div class="md:col-span-3">
                            <select x-model="newExpense.category"
                                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                                <option value="general">General</option>
                                <option value="materials">Materials</option>
                                <option value="labor">Labor</option>
                                <option value="travel">Travel</option>
                                <option value="equipment">Equipment</option>
                                <option value="software">Software</option>
                                <option value="subcontractor">Subcontractor</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                            <div class="md:col-span-9">
                                <textarea x-model="newExpense.description" placeholder="Description (optional)" rows="3"
                                          class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500"></textarea>
                            </div>
                            <div class="md:col-span-3 md:flex md:justify-end">
                                <button type="submit" :disabled="expenseLoading"
                                        class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition whitespace-nowrap">
                                    <svg x-show="expenseLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Add Expense
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Expenses List -->
                <div id="expenses-list" class="space-y-3">
                    @forelse($project->expenses as $expense)
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg" id="expense-{{ $expense->id }}">
                            <div class="flex items-center space-x-4 flex-1 min-w-0">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $expense->title }}</p>
                                    <div class="flex items-center space-x-3 mt-1">
                                        <span class="text-xs font-medium text-red-600">₦{{ number_format($expense->amount, 2) }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ ucfirst($expense->category) }}</span>
                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</span>
                                        @if($expense->creator)
                                            <span class="text-xs text-gray-400">by {{ $expense->creator->name }}</span>
                                        @endif
                                        @if($expense->voucher)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ $expense->voucher->voucher_number }}</span>
                                        @endif
                                    </div>
                                    @if($expense->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $expense->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <button onclick="deleteExpense({{ $expense->id }})" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete expense">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div id="expenses-empty-state" class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p>No expenses recorded yet. Add project costs above — they'll be posted to accounting automatically.</p>
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
            milestoneAmountDisplay: '',
            milestoneLoading: false,
            // Note
            newNote: { content: '', is_internal: true },
            noteLoading: false,
            // File
            selectedFile: null,
            fileLoading: false,
            // Expense
            newExpense: { title: '', amount: '', expense_date: '', category: 'general', description: '' },
            expenseAmountDisplay: '',
            expenseLoading: false,

            setExpenseAmount(value) {
                const sanitized = value.replace(/,/g, '').replace(/[^\d.]/g, '');

                if (!sanitized) {
                    this.newExpense.amount = '';
                    this.expenseAmountDisplay = '';
                    return;
                }

                const hasDecimal = sanitized.includes('.');
                const [wholePartRaw, ...decimalParts] = sanitized.split('.');
                const wholePart = wholePartRaw.replace(/^0+(?=\d)/, '');
                const decimalPart = decimalParts.join('').slice(0, 2);
                const normalizedWhole = wholePart === '' ? '0' : wholePart;

                this.newExpense.amount = hasDecimal
                    ? `${normalizedWhole}.${decimalPart}`
                    : normalizedWhole;

                const formattedWhole = Number(normalizedWhole).toLocaleString('en-NG');
                this.expenseAmountDisplay = hasDecimal
                    ? `${formattedWhole}.${decimalPart}`
                    : formattedWhole;
            },

            setMilestoneAmount(value) {
                const sanitized = value.replace(/,/g, '').replace(/[^\d.]/g, '');

                if (!sanitized) {
                    this.newMilestone.amount = '';
                    this.milestoneAmountDisplay = '';
                    return;
                }

                const hasDecimal = sanitized.includes('.');
                const [wholePartRaw, ...decimalParts] = sanitized.split('.');
                const wholePart = wholePartRaw.replace(/^0+(?=\d)/, '');
                const decimalPart = decimalParts.join('').slice(0, 2);
                const normalizedWhole = wholePart === '' ? '0' : wholePart;

                this.newMilestone.amount = hasDecimal
                    ? `${normalizedWhole}.${decimalPart}`
                    : normalizedWhole;

                const formattedWhole = Number(normalizedWhole).toLocaleString('en-NG');
                this.milestoneAmountDisplay = hasDecimal
                    ? `${formattedWhole}.${decimalPart}`
                    : formattedWhole;
            },

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
                    const data = await res.json();
                    if (res.ok) {
                        appendMilestoneRow(data.milestone);
                        updateCount('milestones-count-badge', 1);
                        this.newMilestone = { title: '', amount: '', due_date: '', is_billable: true };
                        this.milestoneAmountDisplay = '';
                    } else {
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
                    const data = await res.json();
                    if (res.ok) {
                        appendNoteRow(data.note);
                        updateCount('notes-count-badge', 1);
                        this.newNote = { content: '', is_internal: true };
                    } else {
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
            },

            async addExpense() {
                this.expenseLoading = true;
                try {
                    const res = await fetch(`${BASE_URL}/expenses`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                        body: JSON.stringify(this.newExpense)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        appendExpenseRow(data.expense);
                        updateExpensesCount(1);
                        updateBudgetSummary(data.project_actual_cost, data.budget_used_percent);
                        this.newExpense = { title: '', amount: '', expense_date: '', category: 'general', description: '' };
                        this.expenseAmountDisplay = '';
                    } else {
                        alert(data.message || 'Failed to record expense');
                    }
                } catch (e) { alert('Error recording expense'); }
                this.expenseLoading = false;
            }
        };
    }

    async function updateTaskStatus(taskId, status) {
        const borderMap = {
            todo: 'border-l-gray-400',
            in_progress: 'border-l-blue-500',
            review: 'border-l-yellow-500',
            done: 'border-l-green-500',
        };
        try {
            const res = await fetch(`${BASE_URL}/tasks/${taskId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            if (res.ok) {
                const row = document.getElementById(`task-${taskId}`);
                if (row) {
                    // Update left-border colour
                    Object.values(borderMap).forEach(cls => row.classList.remove(cls));
                    row.classList.add(borderMap[status] ?? 'border-l-gray-300');

                    // Toggle strikethrough on title
                    const title = row.querySelector('p.font-medium');
                    if (title) {
                        if (status === 'done') {
                            title.classList.add('line-through', 'text-gray-400');
                            title.classList.remove('text-gray-900');
                        } else {
                            title.classList.remove('line-through', 'text-gray-400');
                            title.classList.add('text-gray-900');
                        }
                    }
                }
            } else {
                const data = await res.json();
                alert(data.message || 'Failed to update task');
            }
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
                if (el) {
                    el.remove();
                    updateCount('milestones-count-badge', -1);
                    ensureMilestoneEmptyState();
                }
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
                if (el) {
                    el.remove();
                    updateCount('notes-count-badge', -1);
                    ensureNotesEmptyState();
                }
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

    async function invoiceMilestone(milestoneId) {
        if (!confirm('Create an accounting voucher for this milestone?')) return;
        try {
            const res = await fetch(`${BASE_URL}/milestones/${milestoneId}/invoice`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Milestone invoiced successfully.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to invoice milestone');
            }
        } catch (e) { alert('Error invoicing milestone'); }
    }

    async function deleteExpense(expenseId) {
        if (!confirm('Delete this expense? The accounting entry will also be reversed.')) return;
        try {
            const res = await fetch(`${BASE_URL}/expenses/${expenseId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
            if (res.ok) {
                const el = document.getElementById(`expense-${expenseId}`);
                if (el) {
                    el.remove();
                    updateExpensesCount(-1);
                    ensureExpenseEmptyState();
                }
            }
        } catch (e) { alert('Error deleting expense'); }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCurrency(amount) {
        const numericAmount = Number(amount || 0);
        return `₦${numericAmount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function formatExpenseDate(dateValue) {
        if (!dateValue) return '';
        return new Date(dateValue).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    function formatExpenseCategory(category) {
        return String(category || 'general')
            .split('_')
            .map(part => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
    }

    function updateCount(badgeId, delta) {
        const badge = document.getElementById(badgeId);
        if (!badge) return;

        const nextValue = Math.max(0, (parseInt(badge.textContent, 10) || 0) + delta);
        badge.textContent = nextValue;
    }

    function updateExpensesCount(delta) {
        updateCount('expenses-count-badge', delta);
    }

    function updateBudgetSummary(actualCost, budgetUsedPercent) {
        const amountEl = document.getElementById('budget-spent-amount');
        const percentEl = document.getElementById('budget-used-percent');

        if (amountEl) {
            amountEl.textContent = Number(actualCost || 0).toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        if (percentEl && budgetUsedPercent !== undefined && budgetUsedPercent !== null) {
            percentEl.textContent = budgetUsedPercent;
        }
    }

    function appendExpenseRow(expense) {
        const list = document.getElementById('expenses-list');
        if (!list || !expense) return;

        const emptyState = document.getElementById('expenses-empty-state');
        if (emptyState) emptyState.remove();

        const descriptionHtml = expense.description
            ? `<p class="text-xs text-gray-500 mt-1">${escapeHtml(expense.description)}</p>`
            : '';
        const creatorHtml = expense.creator?.name
            ? `<span class="text-xs text-gray-400">by ${escapeHtml(expense.creator.name)}</span>`
            : '';
        const voucherHtml = expense.voucher?.voucher_number
            ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">${escapeHtml(expense.voucher.voucher_number)}</span>`
            : '';

        const row = document.createElement('div');
        row.id = `expense-${expense.id}`;
        row.className = 'flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg';
        row.innerHTML = `
            <div class="flex items-center space-x-4 flex-1 min-w-0">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">${escapeHtml(expense.title)}</p>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-xs font-medium text-red-600">${formatCurrency(expense.amount)}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">${escapeHtml(formatExpenseCategory(expense.category))}</span>
                        <span class="text-xs text-gray-500">${escapeHtml(formatExpenseDate(expense.expense_date))}</span>
                        ${creatorHtml}
                        ${voucherHtml}
                    </div>
                    ${descriptionHtml}
                </div>
            </div>
            <button onclick="deleteExpense(${expense.id})" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete expense">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;

        list.prepend(row);
    }

    function appendMilestoneRow(milestone) {
        const list = document.getElementById('milestones-list');
        if (!list || !milestone) return;

        const emptyState = document.getElementById('milestones-empty-state');
        if (emptyState) emptyState.remove();

        const amountHtml = milestone.amount
            ? `<span class="text-xs font-medium ${milestone.is_billable ? 'text-violet-600' : 'text-gray-500'}">${formatCurrency(milestone.amount)}${milestone.is_billable ? ' (Billable)' : ''}</span>`
            : '';
        const dueDateHtml = milestone.due_date
            ? `<span class="text-xs text-gray-500">Due: ${escapeHtml(formatExpenseDate(milestone.due_date))}</span>`
            : '';

        const row = document.createElement('div');
        row.id = `milestone-${milestone.id}`;
        row.className = 'flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg';
        row.innerHTML = `
            <div class="flex items-center space-x-4 flex-1 min-w-0">
                <button onclick="toggleMilestone(${milestone.id}, true)"
                        class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors duration-200 border-gray-300 hover:border-green-400">
                </button>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">${escapeHtml(milestone.title)}</p>
                    <div class="flex items-center space-x-3 mt-1">
                        ${amountHtml}
                        ${dueDateHtml}
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 ml-3">
                <button onclick="deleteMilestone(${milestone.id})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete milestone">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;

        list.prepend(row);
    }

    function appendNoteRow(note) {
        const list = document.getElementById('notes-list');
        if (!list || !note) return;

        const emptyState = document.getElementById('notes-empty-state');
        if (emptyState) emptyState.remove();

        const initials = (note.user?.name || '?').substring(0, 2).toUpperCase();
        const internalHtml = note.is_internal
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Internal</span>'
            : '';

        const row = document.createElement('div');
        row.id = `note-${note.id}`;
        row.className = 'flex space-x-3';
        row.innerHTML = `
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-violet-600">${escapeHtml(initials)}</span>
                </div>
            </div>
            <div class="flex-1 bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(note.user?.name || 'Unknown')}</span>
                        <span class="text-xs text-gray-400">Just now</span>
                        ${internalHtml}
                    </div>
                    <button onclick="deleteNote(${note.id})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete note">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-line">${escapeHtml(note.content)}</p>
            </div>
        `;

        list.prepend(row);
    }

    function ensureExpenseEmptyState() {
        const list = document.getElementById('expenses-list');
        if (!list || list.children.length > 0) return;

        const emptyState = document.createElement('div');
        emptyState.id = 'expenses-empty-state';
        emptyState.className = 'text-center py-8 text-gray-400';
        emptyState.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p>No expenses recorded yet. Add project costs above — they'll be posted to accounting automatically.</p>
        `;

        list.appendChild(emptyState);
    }

    function ensureMilestoneEmptyState() {
        const list = document.getElementById('milestones-list');
        if (!list || list.children.length > 0) return;

        const emptyState = document.createElement('div');
        emptyState.id = 'milestones-empty-state';
        emptyState.className = 'text-center py-8 text-gray-400';
        emptyState.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
            </svg>
            <p>No milestones yet. Add one above to track project deliverables.</p>
        `;

        list.appendChild(emptyState);
    }

    function ensureNotesEmptyState() {
        const list = document.getElementById('notes-list');
        if (!list || list.children.length > 0) return;

        const emptyState = document.createElement('div');
        emptyState.id = 'notes-empty-state';
        emptyState.className = 'text-center py-8 text-gray-400';
        emptyState.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            <p>No notes yet. Add one to keep track of important information.</p>
        `;

        list.appendChild(emptyState);
    }
</script>
@endpush
