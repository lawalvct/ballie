@extends('layouts.tenant')

@section('title', 'Tax Report')
@section('page-title', 'Tax Report')
@section('page-description', 'Employee tax summary and PAYE reports')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Tax Report</h1>
                    <p class="text-orange-100 text-lg">Employee tax summary and PAYE reports</p>
                </div>
                <a href="{{ route('tenant.payroll.index', $tenant) }}"
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Payroll
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Filter Options</h3>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select name="year" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Month (Optional)</label>
                    <select name="month" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="exportReport()"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Employees</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $taxData ? $taxData->count() : 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-green-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Gross Salary</p>
                        <p class="text-2xl font-bold text-gray-900">
                            ₦{{ number_format($taxData ? $taxData->sum('total_gross') : 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <i class="fas fa-receipt text-orange-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total PAYE Tax</p>
                        <p class="text-2xl font-bold text-gray-900">
                            ₦{{ number_format($taxData ? $taxData->sum('total_tax') : 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <i class="fas fa-percentage text-purple-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg Tax Rate</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @if($taxData && $taxData->sum('total_gross') > 0)
                                {{ number_format(($taxData->sum('total_tax') / $taxData->sum('total_gross')) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Data Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Employee Tax Summary</h3>
                <p class="text-gray-600 mt-1">
                    Tax report for {{ $year ?? date('Y') }}
                    @if($month ?? false)
                        - {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                    @endif
                </p>
            </div>

            @if($taxData && $taxData->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gross</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tax</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Rate</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payroll Runs</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($taxData as $employeeData)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ $employeeData['employee']->first_name }} {{ $employeeData['employee']->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $employeeData['employee']->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $employeeData['employee']->employee_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $employeeData['employee']->department->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ₦{{ number_format($employeeData['total_gross'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange-600">
                                        ₦{{ number_format($employeeData['total_tax'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($employeeData['total_gross'] > 0)
                                            {{ number_format(($employeeData['total_tax'] / $employeeData['total_gross']) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $employeeData['runs']->count() }} runs
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-file-invoice text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No tax data found</h3>
                    <p class="text-gray-500 mb-6">No payroll data available for the selected period.</p>
                    <a href="{{ route('tenant.payroll.processing.index', $tenant) }}"
                       class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>Process Payroll
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportReport() {
    const year = document.querySelector('select[name="year"]').value;
    const month = document.querySelector('select[name="month"]').value;

    let url = `/tenant/{{ $tenant->id }}/payroll/reports/tax-report/export?year=${year}`;
    if (month) {
        url += `&month=${month}`;
    }

    window.open(url, '_blank');
}
</script>
@endsection
