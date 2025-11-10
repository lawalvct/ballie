@extends('layouts.tenant')

@section('title', 'Monthly Attendance Report')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Monthly Attendance Report</h1>
                    <p class="text-purple-100 text-lg">{{ $startDate->format('F Y') }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <form method="GET" action="{{ route('tenant.payroll.attendance.monthly-report', $tenant) }}" class="flex items-center space-x-2">
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
                        <button type="submit" class="px-6 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg font-medium transition-all">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="{{ route('tenant.payroll.attendance.index', $tenant) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Daily
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Export Options -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Report Period</h3>
                    <p class="text-gray-600">{{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-print mr-2"></i>Print Report
                    </button>
                    <button onclick="exportToCSV()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Employee Summary Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Employee Attendance Summary</h3>
                <p class="text-gray-600 mt-1">Detailed breakdown for all employees</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full" id="attendanceTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Late</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">On Leave</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Half Day</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance %</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                            @php
                                $summary = $employee->attendance_summary;
                                $workingDays = $summary['present'] + $summary['late'];
                                $totalPossible = $startDate->diffInWeekdays($endDate) + 1;
                                $attendancePercentage = $totalPossible > 0 ? round(($workingDays / $totalPossible) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $employee->employee_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->department->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $summary['total_days'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        {{ $summary['present'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        {{ $summary['late'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        {{ $summary['absent'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        {{ $summary['on_leave'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                        {{ $summary['half_day'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                    {{ number_format($summary['total_hours'], 2) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-blue-600">
                                    {{ number_format($summary['total_overtime'], 2) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="text-sm font-bold
                                            {{ $attendancePercentage >= 90 ? 'text-green-600' : '' }}
                                            {{ $attendancePercentage >= 70 && $attendancePercentage < 90 ? 'text-yellow-600' : '' }}
                                            {{ $attendancePercentage < 70 ? 'text-red-600' : '' }}">
                                            {{ $attendancePercentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('tenant.payroll.attendance.employee', [$tenant, $employee]) }}?year={{ $year }}&month={{ $month }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i>Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($employees->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-users-slash text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No employees found</h3>
                    <p class="text-gray-500">There are no active employees for this period.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportToCSV() {
    const table = document.getElementById('attendanceTable');
    let csv = [];

    // Headers
    const headers = Array.from(table.querySelectorAll('thead th'))
        .map(th => th.textContent.trim())
        .filter(h => h !== 'Actions');
    csv.push(headers.join(','));

    // Rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'))
            .slice(0, -1) // Exclude actions column
            .map(td => {
                let text = td.textContent.trim();
                // Remove extra whitespace
                text = text.replace(/\s+/g, ' ');
                // Escape commas
                if (text.includes(',')) {
                    text = `"${text}"`;
                }
                return text;
            });
        csv.push(cells.join(','));
    });

    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `attendance_report_{{ $year }}_{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection
