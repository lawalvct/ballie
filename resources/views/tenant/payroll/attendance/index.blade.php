@extends('layouts.tenant')

@section('title', 'Attendance Management')

@section('content')
<div x-data="attendanceManager()" class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Attendance Management</h1>
                    <p class="text-blue-100 text-lg">Track employee attendance and work hours</p>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="date"
                           value="{{ $selectedDate->format('Y-m-d') }}"
                           onchange="window.location.href = '{{ route('tenant.payroll.attendance.index', $tenant) }}?date=' + this.value"
                           class="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button @click="showManualEntryModal = true"
                            class="bg-green-500/90 backdrop-blur-sm hover:bg-green-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-clock mr-2"></i>Manual Entry
                    </button>
                    <button @click="showLeaveModal = true"
                            class="bg-purple-500/90 backdrop-blur-sm hover:bg-purple-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-umbrella-beach mr-2"></i>Mark Leave
                    </button>
                    <a href="{{ route('tenant.payroll.attendance.monthly-report', $tenant) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-calendar-alt mr-2"></i>Monthly Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-3 rounded-xl">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-green-500 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Present</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['present'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-yellow-500 p-3 rounded-xl">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Late</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['late'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-red-500 p-3 rounded-xl">
                        <i class="fas fa-times-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Absent</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-purple-500 p-3 rounded-xl">
                        <i class="fas fa-umbrella-beach text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">On Leave</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['on_leave'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-orange-500 p-3 rounded-xl">
                        <i class="fas fa-adjust text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Half Day</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $stats['half_day'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" action="{{ route('tenant.payroll.attendance.index', $tenant) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                    <select name="employee" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Attendance for {{ $selectedDate->format('F d, Y') }}</h3>
                    <p class="text-gray-600 mt-1">{{ $selectedDate->format('l') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="bulkApprove" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-check-double mr-2"></i>Bulk Approve
                    </button>
                    <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work Hours</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendanceRecords as $record)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox"
                                           value="{{ $record->id }}"
                                           x-model="selectedRecords"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $record->employee->employee_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->employee->department->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->clock_in)
                                        <div class="text-sm font-medium text-gray-900">{{ $record->clock_in->format('h:i A') }}</div>
                                        @if($record->late_minutes > 0)
                                            <div class="text-xs text-red-600">Late: {{ $record->late_minutes }} min</div>
                                        @endif
                                    @else
                                        <button onclick="clockIn({{ $record->employee->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            <i class="fas fa-sign-in-alt mr-1"></i>Clock In
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->clock_out)
                                        <div class="text-sm font-medium text-gray-900">{{ $record->clock_out->format('h:i A') }}</div>
                                        @if($record->early_out_minutes > 0)
                                            <div class="text-xs text-orange-600">Early: {{ $record->early_out_minutes }} min</div>
                                        @endif
                                    @elseif($record->clock_in)
                                        <button onclick="clockOut({{ $record->employee->id }})"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            <i class="fas fa-sign-out-alt mr-1"></i>Clock Out
                                        </button>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($record->work_hours_minutes > 0)
                                        {{ number_format($record->work_hours_minutes / 60, 2) }} hrs
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $record->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $record->status === 'half_day' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $record->status === 'on_leave' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($record->overtime_minutes > 0)
                                        <span class="text-blue-600 font-medium">{{ number_format($record->overtime_minutes / 60, 2) }} hrs</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if(!$record->is_approved)
                                            <form action="{{ route('tenant.payroll.attendance.approve', [$tenant, $record]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-green-600" title="Approved">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @endif

                                        <button onclick="markAbsent({{ $record->employee->id }}, '{{ $selectedDate->format('Y-m-d') }}')"
                                                class="text-red-600 hover:text-red-900" title="Mark Absent">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <form action="{{ route('tenant.payroll.attendance.mark-half-day', [$tenant, $record]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="Mark Half Day">
                                                <i class="fas fa-adjust"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('tenant.payroll.attendance.employee', [$tenant, $record->employee]) }}"
                                           class="text-blue-600 hover:text-blue-900" title="View History">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($attendanceRecords->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No attendance records</h3>
                    <p class="text-gray-500">No employees found for the selected filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('tenant.payroll.attendance.partials.manual-entry-modal')
@include('tenant.payroll.attendance.partials.leave-modal')

@push('scripts')
<script>
function attendanceManager() {
    return {
        selectedRecords: [],
        showManualEntryModal: false,
        showLeaveModal: false,
        manualEntry: {
            employee_id: '',
            date: '{{ $selectedDate->format("Y-m-d") }}',
            clock_in_time: '',
            clock_out_time: '',
            break_minutes: 60,
            notes: ''
        },
        leaveData: {
            employee_id: '',
            date: '{{ $selectedDate->format("Y-m-d") }}',
            leave_type: '',
            reason: ''
        },

        toggleAll(event) {
            if (event.target.checked) {
                this.selectedRecords = Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).map(cb => cb.value);
            } else {
                this.selectedRecords = [];
            }
        },

        calculateWorkHoursPreview() {
            if (!this.manualEntry.clock_in_time || !this.manualEntry.clock_out_time) {
                return '';
            }

            const clockIn = new Date('2000-01-01 ' + this.manualEntry.clock_in_time);
            const clockOut = new Date('2000-01-01 ' + this.manualEntry.clock_out_time);
            const breakMinutes = parseInt(this.manualEntry.break_minutes) || 0;

            const totalMinutes = (clockOut - clockIn) / (1000 * 60);
            const workMinutes = totalMinutes - breakMinutes;
            const workHours = (workMinutes / 60).toFixed(2);

            return `Total work hours: ${workHours} hours (${Math.floor(workMinutes / 60)}h ${workMinutes % 60}m)`;
        },

        submitManualEntry() {
            if (!this.manualEntry.employee_id || !this.manualEntry.date || !this.manualEntry.clock_in_time) {
                alert('Please fill in all required fields');
                return;
            }

            fetch('{{ route('tenant.payroll.attendance.manual-entry', $tenant) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.manualEntry)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message + '\n\nClock In: ' + data.data.clock_in +
                          (data.data.clock_out ? '\nClock Out: ' + data.data.clock_out : '') +
                          '\nWork Hours: ' + data.data.work_hours + ' hrs' +
                          (data.data.overtime_hours > 0 ? '\nOvertime: ' + data.data.overtime_hours + ' hrs' : ''));
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || data.message || 'Failed to record attendance'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while recording attendance');
            });
        },

        submitLeave() {
            if (!this.leaveData.employee_id || !this.leaveData.date || !this.leaveData.leave_type) {
                alert('Please fill in all required fields');
                return;
            }

            fetch('{{ route('tenant.payroll.attendance.mark-leave', $tenant) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.leaveData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || data.message || 'Failed to mark leave'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while marking leave');
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

            fetch('{{ route('tenant.payroll.attendance.bulk-approve', $tenant) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    attendance_ids: this.selectedRecords
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to approve records'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while approving records');
            });
        }
    }
}

function clockIn(employeeId) {
    const notes = prompt('Clock in notes (optional):');

    fetch('{{ route('tenant.payroll.attendance.clock-in', $tenant) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            employee_id: employeeId,
            notes: notes
        })
    })
    .then(response => {
        return response.json().then(data => {
            return { ok: response.ok, status: response.status, data: data };
        });
    })
    .then(({ ok, status, data }) => {
        if (ok && data.success) {
            alert(`Clocked in successfully at ${data.clock_in_time}` +
                  (data.late_minutes > 0 ? `\n⚠️ Late by ${data.late_minutes} minutes` : ''));
            window.location.reload();
        } else {
            // Handle 400 status - already clocked in
            if (status === 400 && data.clock_in_time) {
                alert(`Already clocked in today at ${data.clock_in_time}`);
            } else {
                alert('Error: ' + (data.error || data.message || 'Failed to clock in'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while clocking in');
    });
}

function clockOut(employeeId) {
    const notes = prompt('Clock out notes (optional):');

    fetch('{{ route('tenant.payroll.attendance.clock-out', $tenant) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            employee_id: employeeId,
            notes: notes
        })
    })
    .then(response => {
        return response.json().then(data => {
            return { ok: response.ok, status: response.status, data: data };
        });
    })
    .then(({ ok, status, data }) => {
        if (ok && data.success) {
            let message = `Clocked out successfully at ${data.clock_out_time}\nWork hours: ${data.work_hours} hrs`;
            if (data.overtime_hours > 0) {
                message += `\n⭐ Overtime: ${data.overtime_hours} hrs`;
            }
            alert(message);
            window.location.reload();
        } else {
            // Handle 400 status - not clocked in yet or already clocked out
            if (status === 400) {
                if (data.clock_out_time) {
                    alert(`Already clocked out today at ${data.clock_out_time}`);
                } else {
                    alert('Error: ' + (data.error || 'Must clock in before clocking out'));
                }
            } else {
                alert('Error: ' + (data.error || data.message || 'Failed to clock out'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while clocking out');
    });
}

function markAbsent(employeeId, date) {
    const reason = prompt('Reason for absence:');
    if (!reason) return;

    fetch('{{ route('tenant.payroll.attendance.mark-absent', $tenant) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            employee_id: employeeId,
            date: date,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to mark absent'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endpush
@endsection
