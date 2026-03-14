{{-- Overview Tab --}}
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
