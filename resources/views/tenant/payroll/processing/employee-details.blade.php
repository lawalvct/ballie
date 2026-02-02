@extends('layouts.tenant')

@section('title', 'Employee Payroll Details')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Employee Payroll Details</h1>
                    <p class="text-indigo-100 text-lg">{{ $period->name }} • {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('tenant.payroll.processing.employee-details.export', [$tenant, $period]) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-file-export mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('tenant.payroll.processing.show', [$tenant, $period]) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Period
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Payroll Breakdown</h3>
                <p class="text-gray-600 mt-1">Salary components are listed after Basic Salary.</p>
            </div>

            @if($payrollRuns->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-calculator text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No payroll data</h3>
                    <p class="text-gray-500">Generate payroll to view employee details.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                @foreach($componentColumns as $component)
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $component->component_name }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Salary</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payrollRuns as $run)
                                @php
                                    $detailsMap = $run->details->keyBy('component_name');
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $run->employee->first_name }} {{ $run->employee->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $run->employee->employee_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $run->employee->department->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ₦{{ number_format($run->basic_salary, 2) }}
                                    </td>
                                    @foreach($componentColumns as $component)
                                        @php
                                            $detail = $detailsMap->get($component->component_name);
                                        @endphp
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₦{{ number_format($detail->amount ?? 0, 2) }}
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600">
                                        ₦{{ number_format($run->net_salary, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
