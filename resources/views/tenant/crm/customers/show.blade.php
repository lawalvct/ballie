@extends('layouts.tenant')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')
@section('page-description', 'View customer information and transaction history')

@section('content')
<div class="space-y-6">
    <!-- Customer Header -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                    {{ substr($customer->first_name ?? $customer->company_name ?? 'C', 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        @if($customer->customer_type == 'individual')
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        @else
                            {{ $customer->company_name }}
                        @endif
                    </h1>
                    <div class="flex items-center space-x-4 mt-1">
                        <span class="text-sm text-gray-600">
                            {{ $customer->customer_type == 'individual' ? 'Individual Customer' : 'Business Customer' }}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $customer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('tenant.crm.customers.statement', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    View Statement
                </a>
                <a href="{{ route('tenant.crm.customers.edit', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Customer
                </a>
                <a href="{{ route('tenant.crm.customers.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Customers
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Contact Information
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-gray-900">{{ $customer->email }}</p>
                </div>
                @if($customer->phone)
                <div>
                    <label class="text-sm font-medium text-gray-500">Phone</label>
                    <p class="text-gray-900">{{ $customer->phone }}</p>
                </div>
                @endif
                @if($customer->mobile)
                <div>
                    <label class="text-sm font-medium text-gray-500">Mobile</label>
                    <p class="text-gray-900">{{ $customer->mobile }}</p>
                </div>
                @endif
                @if($customer->customer_type == 'business' && $customer->tax_id)
                <div>
                    <label class="text-sm font-medium text-gray-500">Tax ID</label>
                    <p class="text-gray-900">{{ $customer->tax_id }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Address Information
            </h3>
            <div class="space-y-3">
                @if($customer->address_line1 || $customer->address_line2 || $customer->city || $customer->state || $customer->postal_code || $customer->country)
                    @if($customer->address_line1)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address Line 1</label>
                        <p class="text-gray-900">{{ $customer->address_line1 }}</p>
                    </div>
                    @endif
                    @if($customer->address_line2)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address Line 2</label>
                        <p class="text-gray-900">{{ $customer->address_line2 }}</p>
                    </div>
                    @endif
                    @if($customer->city)
                    <div>
                        <label class="text-sm font-medium text-gray-500">City</label>
                        <p class="text-gray-900">{{ $customer->city }}</p>
                    </div>
                    @endif
                    @if($customer->state)
                    <div>
                        <label class="text-sm font-medium text-gray-500">State</label>
                        <p class="text-gray-900">{{ $customer->state }}</p>
                    </div>
                    @endif
                    @if($customer->postal_code)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Postal Code</label>
                        <p class="text-gray-900">{{ $customer->postal_code }}</p>
                    </div>
                    @endif
                    @if($customer->country)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Country</label>
                        <p class="text-gray-900">{{ $customer->country }}</p>
                    </div>
                    @endif
                @else
                    <p class="text-gray-500 italic">No address information available</p>
                @endif
            </div>
        </div>

        <!-- Financial Information -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Financial Information
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Total Spent</label>
                    <p class="text-2xl font-bold text-green-600">₦{{ number_format($customer->total_spent ?? 0, 2) }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Outstanding Balance</label>
                    <p class="text-xl font-semibold text-orange-600">₦{{ number_format($outstandingBalance, 2) }}</p>
                </div>
                @if($customer->currency)
                <div>
                    <label class="text-sm font-medium text-gray-500">Currency</label>
                    <p class="text-gray-900">{{ $customer->currency }}</p>
                </div>
                @endif
                @if($customer->payment_terms)
                <div>
                    <label class="text-sm font-medium text-gray-500">Payment Terms</label>
                    <p class="text-gray-900">{{ $customer->payment_terms }}</p>
                </div>
                @endif
                @if($customer->last_invoice_date)
                <div>
                    <label class="text-sm font-medium text-gray-500">Last Invoice Date</label>
                    <p class="text-gray-900">{{ $customer->last_invoice_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($customer->last_invoice_number)
                <div>
                    <label class="text-sm font-medium text-gray-500">Last Invoice Number</label>
                    <p class="text-gray-900">{{ $customer->last_invoice_number }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    @if($customer->notes)
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Notes
        </h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $customer->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="flex items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-600">Create Invoice</span>
                </div>
            </a>
            <a href="#" class="flex items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-600">Record Payment</span>
                </div>
            </a>
            <a href="#" class="flex items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors duration-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-purple-600">View Reports</span>
                </div>
            </a>
            <a href="mailto:{{ $customer->email }}" class="flex items-center justify-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors duration-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-yellow-600">Send Email</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Transaction History</h3>
            <div class="flex items-center space-x-2">
                <select class="rounded-lg border-gray-300 text-sm">
                    <option>All Transactions</option>
                    <option>Invoices</option>
                    <option>Payments</option>
                    <option>Credits</option>
                </select>
                <select class="rounded-lg border-gray-300 text-sm">
                    <option>Last 30 days</option>
                    <option>Last 3 months</option>
                    <option>Last 6 months</option>
                    <option>Last year</option>
                    <option>All time</option>
                </select>
            </div>
        </div>

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- This would typically be populated with actual transaction data --}}
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No transactions found</p>
                            <p class="text-sm">Start creating invoices or recording payments for this customer.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
            <a href="{{ route('tenant.crm.activities.create', $tenant->slug) }}?customer_id={{ $customer->id }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Log New Activity</a>
        </div>
        @if($activities->count() > 0)
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <span class="h-8 w-8 rounded-full flex items-center justify-center
                                @if($activity->activity_type == 'call') bg-blue-100 text-blue-600
                                @elseif($activity->activity_type == 'email') bg-green-100 text-green-600
                                @elseif($activity->activity_type == 'meeting') bg-purple-100 text-purple-600
                                @elseif($activity->activity_type == 'note') bg-yellow-100 text-yellow-600
                                @elseif($activity->activity_type == 'task') bg-red-100 text-red-600
                                @else bg-orange-100 text-orange-600 @endif">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($activity->activity_type == 'call')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    @elseif($activity->activity_type == 'email')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    @endif
                                </svg>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity->subject }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }} • {{ $activity->user->name }} • {{ $activity->activity_date->format('M d, Y H:i') }}</p>
                            @if($activity->description)
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($activity->description, 100) }}</p>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($activity->status == 'completed') bg-green-100 text-green-800
                            @elseif($activity->status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($activity->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('tenant.crm.activities.index', $tenant->slug) }}?customer_id={{ $customer->id }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Activities →</a>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No activities logged yet. <a href="{{ route('tenant.crm.activities.create', $tenant->slug) }}?customer_id={{ $customer->id }}" class="text-blue-600 hover:text-blue-800">Log your first activity</a></p>
        @endif
    </div>

    <!-- Customer Timeline -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Customer Timeline</h3>
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                <li>
                    <div class="relative pb-8">
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-500">Customer created</p>
                                </div>
                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                    <time datetime="{{ $customer->created_at->toDateString() }}">{{ $customer->created_at->format('M d, Y') }}</time>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @if($customer->updated_at != $customer->created_at)
                <li>
                    <div class="relative pb-8">
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-500">Customer information updated</p>
                                </div>
                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                    <time datetime="{{ $customer->updated_at->toDateString() }}">{{ $customer->updated_at->format('M d, Y') }}</time>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        card.style.transitionDelay = (index * 0.1) + 's';

        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
});
</script>
@endsection
