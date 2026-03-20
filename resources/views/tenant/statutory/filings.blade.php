@extends('layouts.tenant')

@section('title', 'Tax Filing History - ' . $tenant->name)
@section('page-title', 'Tax Filing History')
@section('page-description')
    <span class="hidden md:inline">Track your statutory filing dates, payments, and compliance status</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.statutory.index', $tenant->slug) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Tax
        </a>
        <button onclick="document.getElementById('create-filing-modal').classList.remove('hidden')" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Record Filing
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Overdue Alert -->
    @if($overdueFilings->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">{{ $overdueFilings->count() }} Overdue Filing(s)</h3>
                <div class="mt-1 text-sm text-red-700">
                    @foreach($overdueFilings as $overdue)
                        <p>{{ strtoupper($overdue->type) }} - {{ $overdue->period_label }} (Due: {{ $overdue->due_date->format('M d, Y') }})</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-600">Total Filings</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $filings->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-600">Paid</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $filingSummary['paid'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-600">Filed (Pending)</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $filingSummary['filed'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-600">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $overdueFilings->count() }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tax Type</label>
                <select name="type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Types</option>
                    <option value="vat" {{ request('type') === 'vat' ? 'selected' : '' }}>VAT</option>
                    <option value="paye" {{ request('type') === 'paye' ? 'selected' : '' }}>PAYE</option>
                    <option value="pension" {{ request('type') === 'pension' ? 'selected' : '' }}>Pension</option>
                    <option value="nsitf" {{ request('type') === 'nsitf' ? 'selected' : '' }}>NSITF</option>
                    <option value="wht" {{ request('type') === 'wht' ? 'selected' : '' }}>WHT</option>
                    <option value="cit" {{ request('type') === 'cit' ? 'selected' : '' }}>CIT</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="filed" {{ request('status') === 'filed' ? 'selected' : '' }}>Filed</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Years</option>
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Filter</button>
                <a href="{{ route('tenant.statutory.filings.index', $tenant->slug) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <!-- Filings Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filed By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($filings as $filing)
                    <tr class="{{ $filing->status === 'overdue' || ($filing->due_date && $filing->due_date->isPast() && $filing->status !== 'paid') ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $filing->type === 'vat' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $filing->type === 'paye' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $filing->type === 'pension' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $filing->type === 'nsitf' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $filing->type === 'wht' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $filing->type === 'cit' ? 'bg-indigo-100 text-indigo-800' : '' }}
                            ">{{ strtoupper($filing->type) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $filing->period_label }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">₦{{ number_format($filing->amount, 2) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $filing->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $filing->status === 'filed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $filing->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $filing->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                            ">{{ ucfirst($filing->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $filing->due_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $filing->filer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $filing->reference_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($filing->status !== 'paid')
                                <form method="POST" action="{{ route('tenant.statutory.filings.update-status', [$tenant->slug, $filing->id]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($filing->status === 'draft')
                                        <input type="hidden" name="status" value="filed">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Mark Filed</button>
                                    @elseif($filing->status === 'filed' || $filing->status === 'overdue')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Mark Paid</button>
                                    @endif
                                </form>
                                @endif
                                <form method="POST" action="{{ route('tenant.statutory.filings.destroy', [$tenant->slug, $filing->id]) }}" class="inline" onsubmit="return confirm('Delete this filing record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                            No tax filing records found. Click "Record Filing" to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($filings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $filings->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create Filing Modal -->
<div id="create-filing-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="document.getElementById('create-filing-modal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Record Tax Filing</h3>
            <form method="POST" action="{{ route('tenant.statutory.filings.store', $tenant->slug) }}">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Type</label>
                            <select name="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="vat">VAT</option>
                                <option value="paye">PAYE</option>
                                <option value="pension">Pension</option>
                                <option value="nsitf">NSITF</option>
                                <option value="wht">WHT</option>
                                <option value="cit">CIT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="draft">Draft</option>
                                <option value="filed">Filed</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period Label</label>
                        <input type="text" name="period_label" placeholder="e.g. January 2026, Q1 2026" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Period Start</label>
                            <input type="date" name="period_start" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Period End</label>
                            <input type="date" name="period_end" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                            <input type="number" name="amount" step="0.01" min="0" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input type="text" name="reference_number" placeholder="e.g. FIRS-2026-001" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Optional notes"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('create-filing-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Save Filing</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
