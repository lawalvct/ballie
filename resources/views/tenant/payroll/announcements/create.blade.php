@extends('layouts.tenant')

@section('title', 'Send Announcement - ' . $tenant->name)

@section('page-title', 'Send Announcement')
@section('page-description', 'Create and send announcements to your employees')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('tenant.payroll.announcements.store', ['tenant' => $tenant->slug]) }}"
          method="POST"
          enctype="multipart/form-data"
          id="announcementForm">
        @csrf

        <div class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Announcement Details
                    </h3>

                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('title') border-red-300 @enderror"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message"
                                      id="message"
                                      rows="6"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('message') border-red-300 @enderror"
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select name="priority"
                                        id="priority"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('priority') border-red-300 @enderror"
                                        required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Delivery Method -->
                            <div>
                                <label for="delivery_method" class="block text-sm font-medium text-gray-700">
                                    Delivery Method <span class="text-red-500">*</span>
                                </label>
                                <select name="delivery_method"
                                        id="delivery_method"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('delivery_method') border-red-300 @enderror"
                                        required>
                                    <option value="email" {{ old('delivery_method', 'email') == 'email' ? 'selected' : '' }}>Email Only</option>
                                    <option value="sms" {{ old('delivery_method') == 'sms' ? 'selected' : '' }}>SMS Only</option>
                                    <option value="both" {{ old('delivery_method') == 'both' ? 'selected' : '' }}>Email & SMS</option>
                                </select>
                                @error('delivery_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Attachment -->
                        <div>
                            <label for="attachment" class="block text-sm font-medium text-gray-700">
                                Attachment (Optional)
                            </label>
                            <input type="file"
                                   name="attachment"
                                   id="attachment"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-primary-50 file:text-primary-700
                                          hover:file:bg-primary-100
                                          @error('attachment') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max: 5MB)</p>
                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Recipients
                    </h3>

                    <div class="space-y-4">
                        <!-- Recipient Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Send To <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio"
                                           name="recipient_type"
                                           value="all"
                                           {{ old('recipient_type', 'all') == 'all' ? 'checked' : '' }}
                                           class="form-radio h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           onchange="updateRecipientFields()">
                                    <span class="ml-2 text-sm text-gray-700">All Employees</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="radio"
                                           name="recipient_type"
                                           value="department"
                                           {{ old('recipient_type') == 'department' ? 'checked' : '' }}
                                           class="form-radio h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           onchange="updateRecipientFields()">
                                    <span class="ml-2 text-sm text-gray-700">Specific Departments</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="radio"
                                           name="recipient_type"
                                           value="selected"
                                           {{ old('recipient_type') == 'selected' ? 'checked' : '' }}
                                           class="form-radio h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           onchange="updateRecipientFields()">
                                    <span class="ml-2 text-sm text-gray-700">Selected Employees</span>
                                </label>
                            </div>
                            @error('recipient_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department Selection -->
                        <div id="departmentSelection" style="display: none;">
                            <label for="department_ids" class="block text-sm font-medium text-gray-700">
                                Select Departments <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2 space-y-2 max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3">
                                @foreach($departments as $department)
                                <label class="flex items-center py-2 hover:bg-gray-50 rounded px-2">
                                    <input type="checkbox"
                                           name="department_ids[]"
                                           value="{{ $department->id }}"
                                           {{ is_array(old('department_ids')) && in_array($department->id, old('department_ids')) ? 'checked' : '' }}
                                           class="form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                           onchange="updateRecipientPreview()">
                                    <span class="ml-2 text-sm text-gray-700">
                                        {{ $department->name }}
                                        <span class="text-gray-500">({{ $department->employees_count }} employees)</span>
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            @error('department_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Employee Selection -->
                        <div id="employeeSelection" style="display: none;">
                            <label for="employee_ids" class="block text-sm font-medium text-gray-700">
                                Select Employees <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-2">
                                <input type="text"
                                       id="employeeSearch"
                                       placeholder="Search employees..."
                                       class="mb-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                       onkeyup="filterEmployees()">
                                <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3">
                                    @foreach($employees as $employee)
                                    <label class="flex items-center py-2 hover:bg-gray-50 rounded px-2 employee-item">
                                        <input type="checkbox"
                                               name="employee_ids[]"
                                               value="{{ $employee->id }}"
                                               {{ is_array(old('employee_ids')) && in_array($employee->id, old('employee_ids')) ? 'checked' : '' }}
                                               class="form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                               onchange="updateRecipientPreview()">
                                        <span class="ml-2 text-sm text-gray-700">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                            @if($employee->department)
                                                <span class="text-gray-500">({{ $employee->department->name }})</span>
                                            @endif
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @error('employee_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recipient Preview -->
                        <div id="recipientPreview" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm font-medium text-blue-900">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="recipientCount">Calculating recipients...</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Additional Options
                    </h3>

                    <div class="space-y-4">
                        <!-- Require Acknowledgment -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="requires_acknowledgment"
                                       id="requires_acknowledgment"
                                       value="1"
                                       {{ old('requires_acknowledgment') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="requires_acknowledgment" class="font-medium text-gray-700">
                                    Require Acknowledgment
                                </label>
                                <p class="text-gray-500">Employees must acknowledge they have read this announcement</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Schedule For Later -->
                            <div>
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">
                                    Schedule For Later (Optional)
                                </label>
                                <input type="datetime-local"
                                       name="scheduled_at"
                                       id="scheduled_at"
                                       value="{{ old('scheduled_at') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('scheduled_at') border-red-300 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to send immediately</p>
                                @error('scheduled_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expiration Date -->
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">
                                    Expires At (Optional)
                                </label>
                                <input type="datetime-local"
                                       name="expires_at"
                                       id="expires_at"
                                       value="{{ old('expires_at') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('expires_at') border-red-300 @enderror">
                                <p class="mt-1 text-xs text-gray-500">When this announcement becomes outdated</p>
                                @error('expires_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('tenant.payroll.announcements.index', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Cancel
                        </a>

                        <div class="flex items-center space-x-3">
                            <button type="submit"
                                    name="action"
                                    value="draft"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save as Draft
                            </button>

                            <button type="submit"
                                    name="action"
                                    value="send"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span id="sendButtonText">Send Now</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateRecipientFields();
        updateRecipientPreview();
    });

    // Update visible fields based on recipient type
    function updateRecipientFields() {
        const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
        const departmentSelection = document.getElementById('departmentSelection');
        const employeeSelection = document.getElementById('employeeSelection');

        departmentSelection.style.display = recipientType === 'department' ? 'block' : 'none';
        employeeSelection.style.display = recipientType === 'selected' ? 'block' : 'none';

        updateRecipientPreview();
    }

    // Update recipient count preview
    function updateRecipientPreview() {
        const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
        const recipientCountSpan = document.getElementById('recipientCount');
        let count = 0;

        if (recipientType === 'all') {
            count = {{ $employees->count() }};
            recipientCountSpan.textContent = `This announcement will be sent to all ${count} active employees`;
        } else if (recipientType === 'department') {
            const selectedDepartments = document.querySelectorAll('input[name="department_ids[]"]:checked');
            selectedDepartments.forEach(checkbox => {
                const label = checkbox.parentElement.querySelector('.text-gray-500');
                if (label) {
                    const match = label.textContent.match(/\((\d+) employees\)/);
                    if (match) {
                        count += parseInt(match[1]);
                    }
                }
            });
            recipientCountSpan.textContent = count > 0
                ? `This announcement will be sent to ${count} employee${count !== 1 ? 's' : ''} in ${selectedDepartments.length} department${selectedDepartments.length !== 1 ? 's' : ''}`
                : 'Please select at least one department';
        } else if (recipientType === 'selected') {
            const selectedEmployees = document.querySelectorAll('input[name="employee_ids[]"]:checked');
            count = selectedEmployees.length;
            recipientCountSpan.textContent = count > 0
                ? `This announcement will be sent to ${count} selected employee${count !== 1 ? 's' : ''}`
                : 'Please select at least one employee';
        }
    }

    // Filter employees by search
    function filterEmployees() {
        const searchInput = document.getElementById('employeeSearch');
        const searchTerm = searchInput.value.toLowerCase();
        const employeeItems = document.querySelectorAll('.employee-item');

        employeeItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    }

    // Update send button text based on scheduled_at
    document.getElementById('scheduled_at').addEventListener('change', function() {
        const sendButtonText = document.getElementById('sendButtonText');
        if (this.value) {
            sendButtonText.textContent = 'Schedule';
        } else {
            sendButtonText.textContent = 'Send Now';
        }
    });
</script>
@endpush
@endsection
