@extends('layouts.tenant')

@section('title', 'Edit Project')
@section('page-title', 'Edit Project')
@section('page-description')
    <span class="hidden md:inline">
        Update project details for {{ $project->name }}.
    </span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Project
            </a>
        </div>
        <span class="font-mono text-sm text-gray-400">{{ $project->project_number }}</span>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tenant.projects.update', [$tenant->slug, $project->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Project Details -->
        <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-violet-100 text-violet-600 mr-3 text-sm font-semibold">1</span>
                Project Details
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Project Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('name') border-red-300 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('description') border-red-300 @enderror">{{ old('description', $project->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Client -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Client</label>
                    <select name="customer_id" id="customer_id"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="">— Select Client —</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $project->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->first_name }} {{ $customer->last_name }}
                                @if($customer->company_name) ({{ $customer->company_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Project Manager</label>
                    <select name="assigned_to" id="assigned_to"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="">— Select Team Member —</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}" {{ old('assigned_to', $project->assigned_to) == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="draft" {{ old('status', $project->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" required
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                        <option value="low" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority', $project->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Timeline & Budget -->
        <div class="bg-white rounded-2xl p-6 shadow-lg mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-violet-100 text-violet-600 mr-3 text-sm font-semibold">2</span>
                Timeline & Budget
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date"
                           value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date"
                           value="{{ old('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('end_date') border-red-300 @enderror">
                    @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Budget -->
                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700">Budget (₦)</label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₦</span>
                        </div>
                        <input type="number" name="budget" id="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0"
                               class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 @error('budget') border-red-300 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between">
            <!-- Delete -->
            <div>
                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Project
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}"
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-violet-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Project
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('tenant.projects.destroy', [$tenant->slug, $project->id]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
