@extends('layouts.tenant')

@section('title', 'Edit Overtime Record - ' . $tenant->name)
@section('page-title', 'Edit Overtime Record')
@section('page-description', 'Update overtime record information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Edit Overtime Record</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $overtime->overtime_number }}</p>
                </div>
                <a href="{{ route('tenant.overtime.show', [$tenant, $overtime->id]) }}"
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </div>

        <form action="{{ route('tenant.overtime.update', [$tenant, $overtime->id]) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Employee (Read Only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <div class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-900">
                    {{ $overtime->employee->full_name }} - {{ $overtime->employee->employee_number }}
                </div>
            </div>

            <!-- Overtime Date -->
            <div>
                <label for="overtime_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Overtime Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="overtime_date" name="overtime_date"
                       value="{{ old('overtime_date', $overtime->overtime_date->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('overtime_date') border-red-500 @enderror">
                @error('overtime_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="start_time" name="start_time"
                           value="{{ old('start_time', \Carbon\Carbon::parse($overtime->start_time)->format('H:i')) }}" required
                           onchange="calculateHours()"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('start_time') border-red-500 @enderror">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        End Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="end_time" name="end_time"
                           value="{{ old('end_time', \Carbon\Carbon::parse($overtime->end_time)->format('H:i')) }}" required
                           onchange="calculateHours()"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('end_time') border-red-500 @enderror">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Calculated Hours Display -->
            <div id="hours_display" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-900">
                        Total Hours: <span id="total_hours_text" class="font-bold">{{ $overtime->total_hours }}</span> hours
                    </span>
                </div>
            </div>

            <!-- Overtime Type and Multiplier -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="overtime_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Overtime Type <span class="text-red-500">*</span>
                    </label>
                    <select id="overtime_type" name="overtime_type" required
                            onchange="updateMultiplier()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('overtime_type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="weekday" {{ old('overtime_type', $overtime->overtime_type) == 'weekday' ? 'selected' : '' }} data-multiplier="1.5">Weekday (1.5x)</option>
                        <option value="weekend" {{ old('overtime_type', $overtime->overtime_type) == 'weekend' ? 'selected' : '' }} data-multiplier="2.0">Weekend (2.0x)</option>
                        <option value="holiday" {{ old('overtime_type', $overtime->overtime_type) == 'holiday' ? 'selected' : '' }} data-multiplier="2.5">Holiday (2.5x)</option>
                        <option value="emergency" {{ old('overtime_type', $overtime->overtime_type) == 'emergency' ? 'selected' : '' }} data-multiplier="2.0">Emergency (2.0x)</option>
                    </select>
                    @error('overtime_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Hourly Rate (₦) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="hourly_rate" name="hourly_rate"
                           value="{{ old('hourly_rate', $overtime->hourly_rate) }}"
                           step="0.01" min="0" required
                           onchange="calculateAmount()"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('hourly_rate') border-red-500 @enderror">
                    @error('hourly_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Amount Preview -->
            <div id="amount_preview" class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-green-900">Estimated Amount:</span>
                    </div>
                    <span id="estimated_amount" class="text-lg font-bold text-green-900">₦{{ number_format($overtime->total_amount, 2) }}</span>
                </div>
                <p class="text-xs text-green-700 mt-2">
                    <span id="calculation_formula">{{ $overtime->total_hours }} hours × ₦{{ number_format($overtime->hourly_rate, 2) }} × {{ $overtime->multiplier }}x = ₦{{ number_format($overtime->total_amount, 2) }}</span>
                </p>
            </div>

            <!-- Reason -->
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason <span class="text-red-500">*</span>
                </label>
                <textarea id="reason" name="reason" rows="3" required
                          placeholder="Explain why overtime was needed..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('reason') border-red-500 @enderror">{{ old('reason', $overtime->reason) }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Work Description (Optional) -->
            <div>
                <label for="work_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Work Description (Optional)
                </label>
                <textarea id="work_description" name="work_description" rows="3"
                          placeholder="Describe the work performed during overtime..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('work_description') border-red-500 @enderror">{{ old('work_description', $overtime->work_description) }}</textarea>
                @error('work_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('tenant.overtime.show', [$tenant, $overtime->id]) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    Update Overtime Record
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function calculateHours() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;

    if (startTime && endTime) {
        const start = new Date('2000-01-01 ' + startTime);
        const end = new Date('2000-01-01 ' + endTime);

        let hours = (end - start) / (1000 * 60 * 60);

        if (hours < 0) {
            hours += 24; // Handle overnight overtime
        }

        document.getElementById('total_hours_text').textContent = hours.toFixed(2);
        calculateAmount();
    }
}

function updateMultiplier() {
    calculateAmount();
}

function calculateAmount() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    const hourlyRate = parseFloat(document.getElementById('hourly_rate').value) || 0;
    const overtimeType = document.getElementById('overtime_type');
    const selectedOption = overtimeType.options[overtimeType.selectedIndex];
    const multiplier = parseFloat(selectedOption.dataset.multiplier) || 1;

    if (startTime && endTime && hourlyRate > 0 && multiplier > 0) {
        const start = new Date('2000-01-01 ' + startTime);
        const end = new Date('2000-01-01 ' + endTime);

        let hours = (end - start) / (1000 * 60 * 60);
        if (hours < 0) hours += 24;

        const amount = hours * hourlyRate * multiplier;

        document.getElementById('estimated_amount').textContent = '₦' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('calculation_formula').textContent =
            `${hours.toFixed(2)} hours × ₦${hourlyRate.toFixed(2)} × ${multiplier}x = ₦${amount.toFixed(2)}`;
    }
}

// Initial calculation
document.addEventListener('DOMContentLoaded', function() {
    calculateHours();
});
</script>
@endpush
@endsection
