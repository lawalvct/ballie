{{-- Tasks Tab --}}
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
