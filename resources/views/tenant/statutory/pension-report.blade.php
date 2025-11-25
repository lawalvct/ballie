@extends('layouts.tenant')

@section('title', 'Pension Contributions Report')
@section('page-title', 'Pension Contributions Report')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600">Employee Contribution (8%)</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">₦{{ number_format($summary['total_employee_contribution'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600">Employer Contribution (10%)</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">₦{{ number_format($summary['total_employer_contribution'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600">Total Contribution</p>
            <p class="text-2xl font-bold text-purple-600 mt-2">₦{{ number_format($summary['total_contribution'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600">Total Employees</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $summary['employee_count'] }}</p>
        </div>
    </div>

    <!-- Grouped by PFA -->
    @foreach($groupedByPFA as $pfa => $runs)
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">{{ $pfa }}</h3>
            <p class="text-sm text-gray-600">{{ $runs->count() }} employees</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RSA PIN</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic Salary</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Employee (8%)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Employer (10%)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($runs as $run)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $run->employee->full_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $run->employee->rsa_pin ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($run->basic_salary, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($run->pension_employee, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($run->pension_employer, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold text-purple-600">₦{{ number_format($run->pension_employee + $run->pension_employer, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="3" class="px-6 py-4 text-sm text-gray-900">Subtotal for {{ $pfa }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($runs->sum('pension_employee'), 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($runs->sum('pension_employer'), 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-purple-600">₦{{ number_format($runs->sum(fn($r) => $r->pension_employee + $r->pension_employer), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
