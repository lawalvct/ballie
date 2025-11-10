@extends('layouts.tenant')

@section('title', 'Employee Attendance - ' . $employee->full_name)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                        <p class="text-purple-100">{{ $employee->employee_number }} • {{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <form method="GET" class="flex items-center space-x-2">
                        <select name="month" class="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }} style="color: #333;">
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" class="px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                            @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} style="color: #333;">{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="px-6 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg font-medium">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="{{ route('tenant.payroll.attendance.monthly-report', $tenant) }}?year={{ $year }}&month={{ $month }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Present Days</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $summary['present'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Late Days</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $summary['late'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Absent Days</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ $summary['absent'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-xl">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Hours</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($summary['total_hours'], 1) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i class="fas fa-business-time text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Overtime</p>
                        <p class="text-3xl font-bold text-purple-600 mt-1">{{ number_format($summary['total_overtime'], 1) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-stopwatch text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Attendance Calendar - {{ $startDate->format('F Y') }}</h3>

            <div class="grid grid-cols-7 gap-2">
                <!-- Day Headers -->
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center font-semibold text-gray-600 py-2">{{ $day }}</div>
                @endforeach

                <!-- Empty cells for days before month starts -->
                @for($i = 0; $i < $startDate->copy()->startOfMonth()->dayOfWeek; $i++)
                    <div class="aspect-square"></div>
                @endfor

                <!-- Days of month -->
                @php
                    $current = $startDate->copy()->startOfMonth();
                    $end = $startDate->copy()->endOfMonth();
                @endphp

                @while($current <= $end)
                    @php
                        $record = $attendanceRecords->where('attendance_date', $current->format('Y-m-d'))->first();
                        $isWeekend = $current->isWeekend();
                        $isToday = $current->isToday();
                    @endphp

                    <div class="aspect-square border-2 rounded-lg p-2 transition-all duration-200 hover:shadow-md
                        {{ $isToday ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}
                        {{ $isWeekend ? 'bg-gray-50' : '' }}
                        {{ $record && $record->status === 'present' ? 'bg-green-50 border-green-300' : '' }}
                        {{ $record && $record->status === 'late' ? 'bg-yellow-50 border-yellow-300' : '' }}
                        {{ $record && $record->status === 'absent' ? 'bg-red-50 border-red-300' : '' }}
                        {{ $record && $record->status === 'on_leave' ? 'bg-purple-50 border-purple-300' : '' }}
                        {{ $record && $record->status === 'half_day' ? 'bg-orange-50 border-orange-300' : '' }}">

                        <div class="flex flex-col h-full">
                            <div class="text-sm font-semibold text-gray-900 mb-1">{{ $current->format('d') }}</div>

                            @if($record)
                                <div class="flex-1 flex flex-col justify-center items-center">
                                    @if($record->clock_in)
                                        <div class="text-xs text-gray-600">{{ $record->clock_in->format('H:i') }}</div>
                                    @endif

                                    <div class="mt-1">
                                        @if($record->status === 'present')
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        @elseif($record->status === 'late')
                                            <i class="fas fa-clock text-yellow-600"></i>
                                        @elseif($record->status === 'absent')
                                            <i class="fas fa-times-circle text-red-600"></i>
                                        @elseif($record->status === 'on_leave')
                                            <i class="fas fa-umbrella-beach text-purple-600"></i>
                                        @elseif($record->status === 'half_day')
                                            <i class="fas fa-adjust text-orange-600"></i>
                                        @endif
                                    </div>

                                    @if($record->overtime_minutes > 0)
                                        <div class="text-xs text-blue-600 mt-1">
                                            +{{ number_format($record->overtime_minutes / 60, 1) }}h
                                        </div>
                                    @endif
                                </div>
                            @elseif($isWeekend)
                                <div class="flex-1 flex items-center justify-center text-xs text-gray-400">
                                    Weekend
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        $current->addDay();
                    @endphp
                @endwhile
            </div>

            <!-- Legend -->
            <div class="mt-6 flex flex-wrap gap-4 justify-center">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-100 border-2 border-green-300 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Present</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-100 border-2 border-yellow-300 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Late</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-100 border-2 border-red-300 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Absent</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-purple-100 border-2 border-purple-300 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">On Leave</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-orange-100 border-2 border-orange-300 rounded mr-2"></div>
                    <span class="text-sm text-gray-600">Half Day</span>
                </div>
            </div>
        </div>

        <!-- Detailed Records -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Detailed Attendance Records</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work Hours</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attendanceRecords->sortByDesc('attendance_date') as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $record->attendance_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->attendance_date->format('l') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($record->clock_in)
                                        <div class="font-medium text-gray-900">{{ $record->clock_in->format('h:i A') }}</div>
                                        @if($record->late_minutes > 0)
                                            <div class="text-xs text-red-600">Late by {{ $record->late_minutes }} min</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($record->clock_out)
                                        <div class="font-medium text-gray-900">{{ $record->clock_out->format('h:i A') }}</div>
                                        @if($record->early_out_minutes > 0)
                                            <div class="text-xs text-orange-600">Early by {{ $record->early_out_minutes }} min</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($record->work_hours_minutes > 0)
                                        {{ number_format($record->work_hours_minutes / 60, 2) }} hrs
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                    @if($record->overtime_minutes > 0)
                                        {{ number_format($record->overtime_minutes / 60, 2) }} hrs
                                    @else
                                        <span class="text-gray-400">-</span>
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
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $record->absence_reason ?? $record->remarks ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                                    <div class="text-lg font-medium text-gray-900 mb-2">No attendance records</div>
                                    <p class="text-gray-500">No records found for this month.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
