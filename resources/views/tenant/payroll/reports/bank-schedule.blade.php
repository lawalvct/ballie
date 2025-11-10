@extends('layouts.tenant')

@section('title', 'Bank Payment Schedule')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Bank Payment Schedule</h1>
                    <p class="text-purple-100 text-lg">Approved payrolls ready for bank transfer</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tenant.payroll.index', $tenant) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Payroll
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-3 rounded-xl">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Periods</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['total_periods'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="bg-green-500 p-3 rounded-xl">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Employees</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $summary['total_employees'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="bg-purple-500 p-3 rounded-xl">
                        <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Gross Pay</p>
                        <p class="text-xl font-bold text-gray-900">₦{{ number_format($summary['total_gross'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="bg-red-500 p-3 rounded-xl">
                        <i class="fas fa-minus-circle text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Deductions</p>
                        <p class="text-xl font-bold text-gray-900">₦{{ number_format($summary['total_deductions'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="bg-indigo-500 p-3 rounded-xl">
                        <i class="fas fa-university text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Net Pay</p>
                        <p class="text-xl font-bold text-gray-900">₦{{ number_format($summary['total_net'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" action="{{ route('tenant.payroll.reports.bank-schedule', $tenant) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select name="year" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <select name="month" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved (Ready for Payment)</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Payroll Periods List -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Payment Schedule</h3>
                <p class="text-gray-600 mt-1">Approved payroll periods ready for bank transfer</p>
            </div>

            @if($payrollPeriods->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payrollPeriods as $period)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $period->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $period->pay_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $period->payrollRuns->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ₦{{ number_format($period->total_gross ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        ₦{{ number_format($period->total_deductions ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600">
                                        ₦{{ number_format($period->total_net ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            {{ $period->status === 'approved' ? 'bg-green-100 text-green-800' :
                                               ($period->status === 'paid' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            <span class="w-2 h-2 rounded-full mr-2
                                                {{ $period->status === 'approved' ? 'bg-green-400' :
                                                   ($period->status === 'paid' ? 'bg-blue-400' : 'bg-gray-400') }}"></span>
                                            {{ ucfirst($period->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('tenant.payroll.processing.show', [$tenant, $period]) }}"
                                               class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($period->status === 'approved')
                                                <a href="{{ route('tenant.payroll.processing.export-bank-file', [$tenant, $period]) }}"
                                                   class="text-purple-600 hover:text-purple-900" title="Download Bank File">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right text-gray-900">Total:</td>
                                <td class="px-6 py-4 text-gray-900">{{ $summary['total_employees'] }}</td>
                                <td class="px-6 py-4 text-gray-900">₦{{ number_format($summary['total_gross'], 2) }}</td>
                                <td class="px-6 py-4 text-red-600">₦{{ number_format($summary['total_deductions'], 2) }}</td>
                                <td class="px-6 py-4 text-purple-600">₦{{ number_format($summary['total_net'], 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-university text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No bank payments scheduled</h3>
                    <p class="text-gray-500 mb-6">No approved payrolls found for the selected period.</p>
                    <a href="{{ route('tenant.payroll.processing.index', $tenant) }}"
                       class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>View Payroll Processing
                    </a>
                </div>
            @endif
        </div>

        <!-- Help Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-blue-900 mb-2">About Bank Payment Schedule</h3>
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>Purpose:</strong> This report shows all approved payrolls that are ready for bank transfer.</p>
                        <p><strong>Bank File:</strong> Click the download icon to export a CSV file formatted for your bank's bulk payment system.</p>
                        <p><strong>Payment Process:</strong> After downloading the bank file, upload it to your bank's internet banking platform to process the payments.</p>
                        <p><strong>Status:</strong> Payrolls shown as "Approved" are ready for payment. Once paid, update the status accordingly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
