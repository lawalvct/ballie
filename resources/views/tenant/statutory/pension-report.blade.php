@extends('layouts.tenant')

@section('title', 'Pension Contributions Report')
@section('page-title', 'Pension Contributions Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pension Contributions Report</h2>
            <p class="text-gray-600 mt-1">{{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
        @if($payrollRuns->count() > 0)
        <button onclick="window.print()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2">
            <i class="fas fa-print"></i>
            Print Report
        </button>
        @endif
    </div>

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
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">PFA Provider</label>
                <select name="pfa" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Providers</option>
                    @foreach($groupedByPFA as $pfaName => $runs)
                        <option value="{{ $pfaName }}" {{ request('pfa') === $pfaName ? 'selected' : '' }}>{{ $pfaName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('tenant.statutory.pension.report', $tenant) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
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

    <!-- PFA Cards Overview -->
    @if($payrollRuns->count() > 0 && !request('pfa'))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($groupedByPFA as $pfa => $runs)
        <a href="?start_date={{ $startDate }}&end_date={{ $endDate }}&pfa={{ urlencode($pfa) }}" 
           class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $pfa }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $runs->count() }} employees</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Employee (8%):</span>
                    <span class="font-semibold">₦{{ number_format($runs->sum('pension_employee'), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Employer (10%):</span>
                    <span class="font-semibold">₦{{ number_format($runs->sum('pension_employer'), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm pt-2 border-t">
                    <span class="text-gray-900 font-medium">Total:</span>
                    <span class="font-bold text-purple-600">₦{{ number_format($runs->sum(fn($r) => $r->pension_employee + $r->pension_employer), 2) }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    <!-- Individual PFA Details -->
    @if(request('pfa') && isset($groupedByPFA[request('pfa')]))
        @php $runs = $groupedByPFA[request('pfa')]; @endphp
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b bg-purple-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ request('pfa') }}</h3>
                    <p class="text-sm text-gray-600">{{ $runs->count() }} employees</p>
                </div>
                <button onclick="window.print()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
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
                    <tr class="bg-purple-50 font-semibold">
                        <td colspan="3" class="px-6 py-4 text-sm text-gray-900">Total for {{ request('pfa') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($runs->sum('pension_employee'), 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-900">₦{{ number_format($runs->sum('pension_employer'), 2) }}</td>
                        <td class="px-6 py-4 text-sm text-right text-purple-600">₦{{ number_format($runs->sum(fn($r) => $r->pension_employee + $r->pension_employer), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @if($payrollRuns->count() === 0)
        <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Pension Contributions Found</h3>
            <p class="text-gray-600">No payroll runs with pension contributions for the selected period.</p>
        </div>
    @endif
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>
@endsection
