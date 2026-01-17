@extends('layouts.tenant')

@section('title', 'Attendance Management')

@section('page-title')
    Attendance Management
@endsection

@section('page-description')
    Track and manage employee attendance for <span class="font-semibold">{{ $selectedDate->format('F d, Y') }}</span>.
@endsection





@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="attendance()">

    <div class="flex justify-end mb-4">
        <div class="flex items-center space-x-2">
           <input type="date"
       value="{{ $selectedDate->format('Y-m-d') }}"
       onchange="window.location.href = '{{ route('tenant.payroll.attendance.index', $tenant) }}?date=' + this.value"
       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
<a href="{{ route('tenant.payroll.attendance.qr-codes', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h2m12 0h2m-6 0h-2m-2-8h2m-2 4h2m-2 4h2"/>
    </svg> QR Codes
</a>
<a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    <i class="fas fa-clock mr-2"></i> Manage Shifts
</a>
<button type="button" onclick="openManualEntryModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    <i class="fas fa-plus mr-2"></i> Manual Entry
</button>
<button type="button" onclick="openLeaveModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    <i class="fas fa-umbrella-beach mr-2"></i> Mark Leave
</button>
<a href="{{ route('tenant.payroll.attendance.monthly-report', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    <i class="fas fa-calendar-alt mr-2"></i> Monthly Report
</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users fa-2x text-gray-400"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Employees</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-green-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Present</dt>
                            <dd class="text-3xl font-bold text-green-600">{{ $stats['present'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x text-yellow-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Late</dt>
                            <dd class="text-3xl font-bold text-yellow-600">{{ $stats['late'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle fa-2x text-red-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Absent</dt>
                            <dd class="text-3xl font-bold text-red-600">{{ $stats['absent'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-umbrella-beach fa-2x text-purple-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">On Leave</dt>
                            <dd class="text-3xl font-bold text-purple-600">{{ $stats['on_leave'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-adjust fa-2x text-orange-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Half Day</dt>
                            <dd class="text-3xl font-bold text-orange-600">{{ $stats['half_day'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-8">
        <form method="GET" action="{{ route('tenant.payroll.attendance.index', $tenant) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                <select id="department" name="department_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="shift" class="block text-sm font-medium text-gray-700">Shift</label>
                <select id="shift" name="shift_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Shifts</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                </select>
            </div>
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700">Employee</label>
                <select id="employee" name="employee_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>

        <!-- Attendance Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Attendance List</h3>
            <button @click="bulkApprove()" :disabled="selectedRecords.length === 0"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check-double mr-2"></i>Bulk Approve (<span x-text="selectedRecords.length"></span>)
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="p-4">
                            <input type="checkbox" @change="toggleAll($event)" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work Hours</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendanceRecords as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <input type="checkbox" :value="{{ $record->id }}" x-model="selectedRecords" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        {{-- Placeholder for image, can be replaced with actual image if available --}}
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">{{ substr($record->employee->first_name, 0, 1) }}{{ substr($record->employee->last_name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->employee->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->employee->department->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    // Get shift from attendance record or employee's current shift assignment
                                    $displayShift = $record->shift ?? $record->employee->currentShiftAssignment?->shift;
                                @endphp
                                @if($displayShift)
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $displayShift->name }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $displayShift->code }} • {{ \Carbon\Carbon::parse($displayShift->start_time)->format('g:i A') }}-{{ \Carbon\Carbon::parse($displayShift->end_time)->format('g:i A') }}</div>
                                @else
                                    <span class="text-gray-400 text-xs italic">No shift assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($record->clock_in)
                                    <div>{{ $record->clock_in->format('h:i A') }}</div>
                                    @if($record->scheduled_in)
                                        <div class="text-xs text-gray-400">Scheduled: {{ $record->scheduled_in->format('g:i A') }}</div>
                                    @endif
                                    @if($record->late_minutes > 0)
                                        <div class="text-xs text-red-500">Late: {{ $record->late_minutes }} min</div>
                                    @endif
                                @else
                                    <button @click="clockIn({{ $record->employee->id }})" class="text-indigo-600 hover:text-indigo-900">Clock In</button>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($record->clock_out)
                                    <div>{{ $record->clock_out->format('h:i A') }}</div>
                                    @if($record->scheduled_out)
                                        <div class="text-xs text-gray-400">Scheduled: {{ $record->scheduled_out->format('g:i A') }}</div>
                                    @endif
                                    @if($record->early_out_minutes > 0)
                                        <div class="text-xs text-orange-500">Early: {{ $record->early_out_minutes }} min</div>
                                    @endif
                                @elseif($record->clock_in)
                                    <button @click="clockOut({{ $record->employee->id }})" class="text-green-600 hover:text-green-900">Clock Out</button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->work_hours_minutes > 0 ? number_format($record->work_hours_minutes / 60, 2) . ' hrs' : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $record->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $record->status === 'half_day' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $record->status === 'on_leave' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium">{{ $record->overtime_minutes > 0 ? number_format($record->overtime_minutes / 60, 2) . ' hrs' : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    @if(!$record->is_approved)
                                        <form action="{{ route('tenant.payroll.attendance.approve', [$tenant, $record]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-green-600" title="Approve"><i class="fas fa-check"></i></button>
                                        </form>
                                    @else
                                        <span class="text-green-500" title="Approved"><i class="fas fa-check-circle"></i></span>
                                    @endif
                                    <button @click="markAbsent({{ $record->employee->id }}, '{{ $selectedDate->format('Y-m-d') }}')" class="text-gray-400 hover:text-red-600" title="Mark Absent"><i class="fas fa-times"></i></button>
                                    <form action="{{ route('tenant.payroll.attendance.mark-half-day', [$tenant, $record]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-orange-600" title="Mark Half Day"><i class="fas fa-adjust"></i></button>
                                    </form>
                                    <a href="{{ route('tenant.payroll.attendance.employee', [$tenant, $record->employee]) }}" class="text-gray-400 hover:text-indigo-600" title="View History"><i class="fas fa-history"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12">
                                <div class="text-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                                    <h3 class="text-lg font-medium text-gray-900">No attendance records found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No employees match the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<!-- Include Modals -->
@include('tenant.payroll.attendance.partials.manual-entry-modal')
@include('tenant.payroll.attendance.partials.leave-modal')

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('attendance', () => ({
        selectedRecords: [],

        toggleAll(event) {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            this.selectedRecords = [];
            checkboxes.forEach(cb => {
                cb.checked = event.target.checked;
                if (event.target.checked) {
                    this.selectedRecords.push(cb.value);
                }
            });
        },

        bulkApprove() {
            if (this.selectedRecords.length === 0) {
                alert('Please select at least one record to approve');
                return;
            }
            if (!confirm(`Approve ${this.selectedRecords.length} attendance record(s)?`)) {
                return;
            }
            this.submitPost('{{ route('tenant.payroll.attendance.bulk-approve', $tenant) }}', { attendance_ids: this.selectedRecords });
        },

        clockIn(employeeId) {
            const notes = prompt('Clock in notes (optional):');
            this.submitPost('{{ route('tenant.payroll.attendance.clock-in', $tenant) }}', { employee_id: employeeId, notes: notes }, (data) => {
                alert(`Clocked in successfully at ${data.clock_in_time}` +
                      (data.late_minutes > 0 ? `\n⚠️ Late by ${data.late_minutes} minutes` : ''));
            });
        },

        clockOut(employeeId) {
            const notes = prompt('Clock out notes (optional):');
            this.submitPost('{{ route('tenant.payroll.attendance.clock-out', $tenant) }}', { employee_id: employeeId, notes: notes }, (data) => {
                let message = `Clocked out successfully at ${data.clock_out_time}\nWork hours: ${data.work_hours} hrs`;
                if (data.overtime_hours > 0) {
                    message += `\n⭐ Overtime: ${data.overtime_hours} hrs`;
                }
                alert(message);
            });
        },

        markAbsent(employeeId, date) {
            const reason = prompt('Reason for absence:');
            if (reason) {
                this.submitPost('{{ route('tenant.payroll.attendance.mark-absent', $tenant) }}', { employee_id: employeeId, date: date, reason: reason });
            }
        },

        submitPost(url, body, successCallback = null) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(body)
            })
            .then(response => response.json().then(data => ({ ok: response.ok, status: response.status, data })))
            .then(({ ok, status, data }) => {
                if (ok && data.success) {
                    if (successCallback) {
                        successCallback(data.data || data);
                    } else {
                        alert(data.message || 'Action completed successfully.');
                    }
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || data.message || 'An unknown error occurred.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
        }
    }));
});

// Keep modal functions separate as they are not tied to the main alpine component
function openManualEntryModal() {
    document.getElementById('manualEntryModal').classList.remove('hidden');
}
function closeManualEntryModal() {
    document.getElementById('manualEntryModal').classList.add('hidden');
}
function openLeaveModal() {
    document.getElementById('leaveModal').classList.remove('hidden');
}
function closeLeaveModal() {
    document.getElementById('leaveModal').classList.add('hidden');
}

function calculateWorkHoursPreview() {
    const clockInTime = document.getElementById('manual_clock_in_time').value;
    const clockOutTime = document.getElementById('manual_clock_out_time').value;
    const breakMinutes = parseInt(document.getElementById('manual_break_minutes').value) || 0;

    if (!clockInTime || !clockOutTime) {
        document.getElementById('workHoursPreview').style.display = 'none';
        return;
    }

    const clockIn = new Date(`2000-01-01T${clockInTime}`);
    const clockOut = new Date(`2000-01-01T${clockOutTime}`);

    if (clockOut < clockIn) {
        clockOut.setDate(clockOut.getDate() + 1); // Handle overnight shifts
    }

    const totalMinutes = (clockOut - clockIn) / (1000 * 60);
    const workMinutes = totalMinutes - breakMinutes;
    const workHours = (workMinutes / 60).toFixed(2);
    const hours = Math.floor(workMinutes / 60);
    const minutes = Math.round(workMinutes % 60);

    document.getElementById('workHoursText').textContent = `Total work hours: ${workHours} hours (${hours}h ${minutes}m)`;
    document.getElementById('workHoursPreview').style.display = 'block';
}

function submitManualEntry(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Manual entry successful.');
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || data.message || 'Failed to save entry.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}

function submitLeave(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Leave marked successfully.');
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || data.message || 'Failed to mark leave.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}
</script>
@endpush
@endsection
