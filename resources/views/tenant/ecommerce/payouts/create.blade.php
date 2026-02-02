@extends('layouts.tenant')

@section('title', 'Request Payout')
@section('page-title', 'Request Payout')
@section('page-description', 'Withdraw funds from your available balance')

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
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Request Payout</h3>
                </div>
                <div class="p-6">
                    @if(!$settings || !$settings->payouts_enabled)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">Payouts are currently disabled. Please contact support for more information.</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <form action="{{ route('tenant.ecommerce.payouts.store', ['tenant' => $tenant->slug]) }}" method="POST" id="payoutForm">
                        @csrf

                        <!-- Balance Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-700 mb-1">Available Balance</p>
                                <p class="text-2xl font-bold text-blue-800">₦{{ number_format($availableBalance, 2) }}</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">{{ $settings->deduction_name }}</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $settings->deduction_description }}</p>
                            </div>
                        </div>

                        <!-- Amount Field -->
                        <div class="mb-6">
                            <label for="requested_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Amount to Withdraw <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                <input type="number"
                                       id="requested_amount"
                                       name="requested_amount"
                                       value="{{ old('requested_amount') }}"
                                       min="{{ $settings->minimum_payout }}"
                                       max="{{ min($availableBalance, $settings->maximum_payout ?? $availableBalance) }}"
                                       step="0.01"
                                       required
                                       class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('requested_amount') border-red-500 @enderror">
                            </div>
                            @error('requested_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Minimum: ₦{{ number_format($settings->minimum_payout, 2) }}
                                @if($settings->maximum_payout)
                                | Maximum: ₦{{ number_format($settings->maximum_payout, 2) }}
                                @endif
                            </p>
                        </div>

                        <!-- Deduction Preview -->
                        <div id="deductionPreview" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg hidden">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">Deduction ({{ $settings->deduction_description }})</p>
                                    <p class="text-lg font-semibold text-red-600" id="deductionAmount">-₦0.00</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">You'll Receive</p>
                                    <p class="text-2xl font-bold text-green-600" id="netAmount">₦0.00</p>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Details Section -->
                        <div class="border-t border-gray-200 pt-6 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Bank Details
                            </h4>

                            <!-- Bank Select -->
                            <div class="mb-4">
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank <span class="text-red-500">*</span>
                                </label>
                                <select id="bank_name"
                                        name="bank_name"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_name') border-red-500 @enderror">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                    <option value="{{ $bank['name'] }}" data-code="{{ $bank['code'] }}" {{ old('bank_name') == $bank['name'] ? 'selected' : '' }}>
                                        {{ $bank['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Number -->
                            <div class="mb-4">
                                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="account_number"
                                       name="account_number"
                                       value="{{ old('account_number') }}"
                                       maxlength="10"
                                       pattern="[0-9]{10}"
                                       placeholder="Enter 10-digit account number"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('account_number') border-red-500 @enderror">
                                @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Name -->
                            <div class="mb-4">
                                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="account_name"
                                       name="account_name"
                                       value="{{ old('account_name') }}"
                                       placeholder="Enter account holder name"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('account_name') border-red-500 @enderror">
                                @error('account_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes (Optional)
                                </label>
                                <textarea id="notes"
                                          name="notes"
                                          rows="2"
                                          placeholder="Any additional notes for your payout request..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms Agreement -->
                        @if($settings->payout_terms)
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" id="agreeTerms" required class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">
                                    I agree to the <button type="button" onclick="document.getElementById('termsModal').classList.remove('hidden')" class="text-blue-600 hover:text-blue-800 underline">payout terms and conditions</button>
                                </span>
                            </label>
                        </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('tenant.ecommerce.payouts.index', ['tenant' => $tenant->slug]) }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Submit Payout Request
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- How It Works -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        How It Works
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800">Submit Request</p>
                            <p class="text-sm text-gray-500">Enter amount and bank details</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800">Review & Approval</p>
                            <p class="text-sm text-gray-500">Our team reviews your request</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800">Processing</p>
                            <p class="text-sm text-gray-500">We process the bank transfer</p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">4</div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800">Completed</p>
                            <p class="text-sm text-gray-500">Funds arrive in your account</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="bg-yellow-50 rounded-lg border border-yellow-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-yellow-200 bg-yellow-100">
                    <h4 class="text-md font-semibold text-yellow-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Important
                    </h4>
                </div>
                <div class="p-6">
                    <ul class="space-y-2 text-sm text-yellow-800">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Double-check your bank details before submitting
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Processing time: {{ $settings->processing_time ?? '3-5 business days' }}
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Cannot cancel once approved
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Contact support if you need help
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if($settings && $settings->payout_terms)
<!-- Terms Modal -->
<div id="termsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('termsModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Payout Terms & Conditions
                        </h3>
                        <div class="mt-2 text-sm text-gray-500 whitespace-pre-line">
                            {{ $settings->payout_terms }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                        onclick="document.getElementById('termsModal').classList.add('hidden')"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('requested_amount');
    const deductionPreview = document.getElementById('deductionPreview');
    const deductionAmount = document.getElementById('deductionAmount');
    const netAmount = document.getElementById('netAmount');

    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;

            if (amount > 0) {
                fetch('{{ route("tenant.ecommerce.payouts.calculate-deduction", ["tenant" => $tenant->slug]) }}?amount=' + amount)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            deductionPreview.classList.remove('hidden');
                            deductionAmount.textContent = '-₦' + data.deduction_amount;
                            netAmount.textContent = '₦' + data.net_amount;
                        }
                    });
            } else {
                deductionPreview.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection
