@extends('layouts.tenant')

@section('title', 'PAYE Tax Report - ' . $tenant->name)
@section('page-title', 'PAYE Tax Report')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #printable-report, #printable-report * { visibility: visible; }
        #printable-report { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between no-print">
        <div>
            {{-- <h2 class="text-2xl font-bold text-gray-900">PAYE Tax Report</h2> --}}
            <p class="text-gray-600 mt-1">{{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        </div>
        <div class="flex space-x-3">
            @if($payrollRuns->count() > 0)
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
            @endif
            <a href="{{ route('tenant.statutory.index', $tenant->slug) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Tax
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 no-print">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Filter</button>
                <a href="{{ route('tenant.statutory.paye.report', $tenant->slug) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <div id="printable-report">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm font-medium text-gray-600">Total Gross Income</p>
                <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_gross'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm font-medium text-gray-600">Total Consolidated Relief</p>
                <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_relief'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm font-medium text-gray-600">Total Taxable Income</p>
                <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($summary['total_taxable'], 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm font-medium text-gray-600">Total PAYE Tax</p>
                <p class="text-xl font-bold text-red-600 mt-1">₦{{ number_format($summary['total_tax'], 2) }}</p>
            </div>
        </div>

        <!-- Employee Tax Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Employee PAYE Breakdown</h3>
                <p class="text-sm text-gray-500">{{ $payrollRuns->unique('employee_id')->count() }} employees</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TIN</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Income</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Relief</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Taxable</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monthly Tax</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($groupedByEmployee as $employeeId => $runs)
                            @php
                                $employee = $runs->first()->employee;
                                $totalGross = $runs->sum('annual_gross');
                                $totalRelief = $runs->sum('consolidated_relief');
                                $totalTaxable = $runs->sum('taxable_income');
                                $totalTax = $runs->sum('monthly_tax');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $employee->tin ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900">₦{{ number_format($totalGross / 12, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-600">₦{{ number_format($totalRelief / 12, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900">₦{{ number_format($totalTaxable / 12, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-red-600">₦{{ number_format($totalTax, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No payroll data found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($payrollRuns->count() > 0)
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td class="px-4 py-3 text-sm" colspan="2">Totals</td>
                            <td class="px-4 py-3 text-sm text-right">₦{{ number_format($summary['total_gross'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right">₦{{ number_format($summary['total_relief'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right">₦{{ number_format($summary['total_taxable'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-red-600">₦{{ number_format($summary['total_tax'], 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
