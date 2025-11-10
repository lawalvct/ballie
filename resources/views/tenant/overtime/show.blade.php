@extends('layouts.tenant')

@section('title', 'Overtime Details - ' . $tenant->name)
@section('page-title', 'Overtime Details')
@section('page-description', 'View overtime record information')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $overtime->overtime_number }}</h2>
            <p class="mt-1 text-sm text-gray-600">Overtime Record Details</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($overtime->status === 'pending')
                <a href="{{ route('tenant.payroll.overtime.edit', [$tenant, $overtime->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
            @endif
            <a href="{{ route('tenant.payroll.overtime.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="bg-white rounded-lg border-l-4 p-4
        {{ $overtime->status === 'pending' ? 'border-yellow-500 bg-yellow-50' : '' }}
        {{ $overtime->status === 'approved' ? 'border-green-500 bg-green-50' : '' }}
        {{ $overtime->status === 'rejected' ? 'border-red-500 bg-red-50' : '' }}
        {{ $overtime->status === 'paid' ? 'border-blue-500 bg-blue-50' : '' }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $overtime->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $overtime->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $overtime->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $overtime->status === 'paid' ? 'bg-blue-100 text-blue-800' : '' }}">
                    {{ ucfirst($overtime->status) }}
                </span>
                @if($overtime->status === 'approved' && !$overtime->is_paid)
                    <span class="ml-2 text-sm text-gray-600">(Payment Pending)</span>
                @endif
            </div>

            @if($overtime->status === 'pending')
                <div class="flex items-center space-x-2">
                    <form action="{{ route('tenant.payroll.overtime.approve', [$tenant, $overtime->id]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Approve this overtime record?')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve
                        </button>
                    </form>
                    <button onclick="openRejectModal()"
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Reject
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Details -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Overtime Information</h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Information -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Employee</label>
                    <div class="text-base font-semibold text-gray-900">{{ $overtime->employee->full_name }}</div>
                    <div class="text-sm text-gray-600">{{ $overtime->employee->employee_number }}</div>
                    <div class="text-sm text-gray-600">{{ $overtime->employee->department->name ?? 'N/A' }}</div>
                </div>

                <!-- Overtime Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Overtime Number</label>
                    <div class="text-base font-semibold text-gray-900">{{ $overtime->overtime_number }}</div>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Date</label>
                    <div class="text-base text-gray-900">{{ $overtime->overtime_date->format('F d, Y') }}</div>
                </div>

                <!-- Time Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Time Range</label>
                    <div class="text-base text-gray-900">
                        {{ \Carbon\Carbon::parse($overtime->start_time)->format('h:i A') }} -
                        {{ \Carbon\Carbon::parse($overtime->end_time)->format('h:i A') }}
                    </div>
                </div>

                <!-- Total Hours -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Hours</label>
                    <div class="text-base font-semibold text-gray-900">{{ $overtime->total_hours }} hours</div>
                </div>

                <!-- Overtime Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Overtime Type</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                        {{ $overtime->overtime_type === 'weekday' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $overtime->overtime_type === 'weekend' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $overtime->overtime_type === 'holiday' ? 'bg-pink-100 text-pink-800' : '' }}
                        {{ $overtime->overtime_type === 'emergency' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($overtime->overtime_type) }} ({{ $overtime->multiplier }}x)
                    </span>
                </div>

                <!-- Hourly Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Hourly Rate</label>
                    <div class="text-base text-gray-900">₦{{ number_format($overtime->hourly_rate, 2) }}</div>
                </div>

                <!-- Total Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Amount</label>
                    <div class="text-2xl font-bold text-indigo-600">₦{{ number_format($overtime->total_amount, 2) }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $overtime->total_hours }} hrs × ₦{{ number_format($overtime->hourly_rate, 2) }} × {{ $overtime->multiplier }}x
                    </div>
                </div>
            </div>

            <!-- Reason -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-500 mb-2">Reason</label>
                <div class="text-base text-gray-900 bg-gray-50 rounded-lg p-4">{{ $overtime->reason }}</div>
            </div>

            <!-- Work Description -->
            @if($overtime->work_description)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Work Description</label>
                    <div class="text-base text-gray-900 bg-gray-50 rounded-lg p-4">{{ $overtime->work_description }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Approval/Rejection Information -->
    @if($overtime->status === 'approved' || $overtime->status === 'rejected' || $overtime->status === 'paid')
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $overtime->status === 'approved' || $overtime->status === 'paid' ? 'Approval' : 'Rejection' }} Information
                </h3>
            </div>

            <div class="p-6">
                @if($overtime->status === 'approved' || $overtime->status === 'paid')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Approved By</label>
                            <div class="text-base text-gray-900">{{ $overtime->approver->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Approved At</label>
                            <div class="text-base text-gray-900">{{ $overtime->approved_at?->format('M d, Y h:i A') }}</div>
                        </div>
                        @if($overtime->approval_remarks)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Approval Remarks</label>
                                <div class="text-base text-gray-900 bg-green-50 rounded-lg p-4">{{ $overtime->approval_remarks }}</div>
                            </div>
                        @endif
                    </div>
                @elseif($overtime->status === 'rejected')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Rejected By</label>
                            <div class="text-base text-gray-900">{{ $overtime->rejector->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Rejected At</label>
                            <div class="text-base text-gray-900">{{ $overtime->rejected_at?->format('M d, Y h:i A') }}</div>
                        </div>
                        @if($overtime->rejection_reason)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Rejection Reason</label>
                                <div class="text-base text-gray-900 bg-red-50 rounded-lg p-4">{{ $overtime->rejection_reason }}</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Payment Information -->
    @if($overtime->is_paid)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Payment Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Paid
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Paid Date</label>
                        <div class="text-base text-gray-900">{{ $overtime->paid_date?->format('M d, Y') }}</div>
                    </div>
                    @if($overtime->payrollRun)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Payroll Run</label>
                            <a href="{{ route('tenant.payroll.processing.show', [$tenant, $overtime->payrollRun->payroll_period_id]) }}"
                               class="text-indigo-600 hover:text-indigo-900">
                                View Payroll Run #{{ $overtime->payrollRun->id }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mt-4">Reject Overtime</h3>
            <p class="text-sm text-gray-500 text-center mt-2">Please provide a reason for rejecting this overtime record.</p>

            <form action="{{ route('tenant.payroll.overtime.reject', [$tenant, $overtime->id]) }}" method="POST" class="mt-6">
                @csrf
                <div>
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});
</script>
@endpush
@endsection
