@extends('layouts.tenant')

@section('title', 'Payout Details - ' . $payout->request_number)
@section('page-title', 'Payout Request #' . $payout->request_number)
@section('page-description', 'View payout request details and status')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('tenant.ecommerce.payouts.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Payouts
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-lg font-semibold text-gray-800">Payout Request #{{ $payout->request_number }}</h3>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'processing' => 'bg-purple-100 text-purple-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$payout->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $payout->status_label }}
                    </span>
                </div>

                <div class="p-6">
                    <!-- Progress Steps -->
                    <div class="mb-8">
                        @php
                            $steps = [
                                ['key' => 'pending', 'label' => 'Submitted'],
                                ['key' => 'approved', 'label' => 'Approved'],
                                ['key' => 'processing', 'label' => 'Processing'],
                                ['key' => 'completed', 'label' => 'Completed'],
                            ];
                            $statusOrder = ['pending' => 1, 'approved' => 2, 'processing' => 3, 'completed' => 4, 'rejected' => 0, 'cancelled' => 0];
                            $currentOrder = $statusOrder[$payout->status] ?? 0;
                        @endphp

                        <div class="relative">
                            <!-- Progress Bar -->
                            <div class="absolute top-5 left-0 w-full h-1 bg-gray-200 rounded">
                                <div class="h-full bg-green-500 rounded transition-all duration-300"
                                     style="width: {{ $payout->progress_percentage }}%"></div>
                            </div>

                            <!-- Steps -->
                            <div class="relative flex justify-between">
                                @foreach($steps as $index => $step)
                                    @php
                                        $stepOrder = $statusOrder[$step['key']];
                                        $isActive = $currentOrder >= $stepOrder && $currentOrder > 0;
                                        $isCurrent = $payout->status === $step['key'];
                                    @endphp
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isActive ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }} z-10">
                                            @if($isActive)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <span class="text-sm font-medium">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <span class="mt-2 text-xs {{ $isCurrent ? 'font-bold text-gray-800' : 'text-gray-500' }}">{{ $step['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Rejection / Cancellation Notice -->
                    @if($payout->status === 'rejected')
                    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-red-800">Request Rejected</h4>
                                <p class="mt-1 text-sm text-red-700">{{ $payout->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($payout->status === 'cancelled')
                    <div class="mb-6 bg-gray-50 border-l-4 border-gray-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-800">Request Cancelled</h4>
                                <p class="mt-1 text-sm text-gray-600">This payout request was cancelled.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Amount Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">Requested Amount</p>
                            <p class="text-2xl font-bold text-gray-800">₦{{ number_format($payout->requested_amount, 2) }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">{{ $payout->deduction_description ?: 'Deduction' }}</p>
                            <p class="text-2xl font-bold text-red-600">-₦{{ number_format($payout->deduction_amount, 2) }}</p>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 mb-1">You'll Receive</p>
                            <p class="text-2xl font-bold text-green-600">₦{{ number_format($payout->net_amount, 2) }}</p>
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Bank Details
                        </h4>
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600 w-1/3">Bank Name</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $payout->bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600">Account Number</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $payout->account_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-600">Account Name</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $payout->account_name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($payout->notes)
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Your Notes</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700">{{ $payout->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Payment Reference -->
                    @if($payout->payment_reference)
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-green-800">Payment Reference</h4>
                                <p class="mt-1 text-sm text-green-700 font-mono">{{ $payout->payment_reference }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Admin Notes -->
                    @if($payout->admin_notes)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">Admin Notes</h4>
                                <p class="mt-1 text-sm text-blue-700">{{ $payout->admin_notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('tenant.ecommerce.payouts.index', ['tenant' => $tenant->slug]) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-center">
                        ← Back to Payouts
                    </a>
                    @if($payout->status === 'pending')
                    <form action="{{ route('tenant.ecommerce.payouts.cancel', ['tenant' => $tenant->slug, 'payout' => $payout->id]) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this payout request?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                            Cancel Request
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Timeline
                    </h4>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Request Submitted -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mt-1.5"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-800">Request Submitted</p>
                                <p class="text-xs text-gray-500">{{ $payout->created_at->format('M d, Y h:i A') }}</p>
                                <p class="text-xs text-gray-500">By: {{ $payout->requester->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($payout->processed_at)
                        <!-- Processing Status -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 {{ in_array($payout->status, ['rejected', 'cancelled']) ? 'bg-red-500' : 'bg-green-500' }} rounded-full mt-1.5"></div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-800">{{ $payout->status_label }}</p>
                                <p class="text-xs text-gray-500">{{ $payout->processed_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Request Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Request Info
                    </h4>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Request Number</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->request_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Status</dt>
                            <dd>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$payout->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $payout->status_label }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Submitted</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->created_at->format('M d, Y') }}</dd>
                        </div>
                        @if($payout->processed_at)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Processed</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $payout->processed_at->format('M d, Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Need Help -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <h4 class="text-md font-semibold text-gray-800 mb-1">Need Help?</h4>
                <p class="text-sm text-gray-500 mb-4">Contact our support team if you have any questions about your payout.</p>
                <a href="mailto:support@ballie.ng"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
