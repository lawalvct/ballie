@extends('layouts.tenant')

@section('title', 'Request Withdrawal')
@section('page-title', 'Request Withdrawal')
@section('page-description', 'Withdraw collected online payments to your bank account')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Link -->
    <div>
        <a href="{{ route('tenant.accounting.payouts.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Withdrawals
        </a>
    </div>

    <!-- Balance Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm mb-1">Available Balance</p>
                <p class="text-3xl font-bold">₦{{ number_format($availableBalance, 2) }}</p>
            </div>
            <div class="bg-white/20 rounded-full p-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 text-blue-100 text-xs">
            Minimum withdrawal: ₦{{ number_format($settings->minimum_payout, 2) }}
            @if($settings->maximum_payout)
                &middot; Maximum: ₦{{ number_format($settings->maximum_payout, 2) }}
            @endif
            &middot; Processing: {{ $settings->processing_time }}
        </div>
    </div>

    <!-- Withdrawal Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Withdrawal Details</h3>
            <p class="text-sm text-gray-500 mt-1">Enter the amount and bank details for your withdrawal</p>
        </div>

        <form method="POST" action="{{ route('tenant.accounting.payouts.store', ['tenant' => $tenant->slug]) }}"
              x-data="withdrawalForm()" @submit.prevent="submitForm($event)">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Amount -->
                <div>
                    <label for="requested_amount" class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">₦</span>
                        <input type="number"
                               name="requested_amount"
                               id="requested_amount"
                               x-model="amount"
                               @input.debounce.500ms="calculateDeduction()"
                               step="0.01"
                               min="{{ $settings->minimum_payout }}"
                               max="{{ $settings->maximum_payout ?? $availableBalance }}"
                               placeholder="0.00"
                               class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg @error('requested_amount') border-red-300 @enderror"
                               required>
                    </div>
                    @error('requested_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Deduction Preview -->
                    <div x-show="deductionCalculated" x-cloak class="mt-3 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Requested Amount</span>
                            <span class="font-medium text-gray-900">₦<span x-text="requestedDisplay"></span></span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600" x-text="deductionLabel"></span>
                            <span class="font-medium text-red-600">-₦<span x-text="deductionDisplay"></span></span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t border-gray-300">
                            <span class="font-semibold text-gray-900">You'll Receive</span>
                            <span class="font-bold text-green-600 text-lg">₦<span x-text="netDisplay"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Bank Details Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-semibold text-gray-800 mb-4">Bank Account Details</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Bank Name -->
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                            <select name="bank_name"
                                    id="bank_name"
                                    x-model="bankName"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('bank_name') border-red-300 @enderror"
                                    required>
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank['name'] }}" data-code="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="bank_code" x-model="bankCode">
                            @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Account Number -->
                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                            <input type="text"
                                   name="account_number"
                                   id="account_number"
                                   maxlength="20"
                                   placeholder="0123456789"
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('account_number') border-red-300 @enderror"
                                   required>
                            @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Account Name -->
                        <div class="md:col-span-2">
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                            <input type="text"
                                   name="account_name"
                                   id="account_name"
                                   placeholder="Account holder name"
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('account_name') border-red-300 @enderror"
                                   required>
                            @error('account_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea name="notes"
                              id="notes"
                              rows="2"
                              maxlength="500"
                              placeholder="Any additional notes for this withdrawal request..."
                              class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Terms -->
                @if($settings->payout_terms)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h5 class="text-xs font-semibold text-yellow-800 mb-1">Terms & Conditions</h5>
                    <p class="text-xs text-yellow-700">{{ $settings->payout_terms }}</p>
                </div>
                @endif
            </div>

            <!-- Submit -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('tenant.accounting.payouts.index', ['tenant' => $tenant->slug]) }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        :disabled="isSubmitting"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                        class="px-6 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                    <span x-show="!isSubmitting">Submit Withdrawal Request</span>
                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function withdrawalForm() {
    return {
        amount: '',
        bankName: '',
        bankCode: '',
        isSubmitting: false,
        deductionCalculated: false,
        requestedDisplay: '0.00',
        deductionDisplay: '0.00',
        netDisplay: '0.00',
        deductionLabel: 'Processing Fee',

        init() {
            this.$watch('bankName', () => {
                const select = document.getElementById('bank_name');
                const selected = select.options[select.selectedIndex];
                this.bankCode = selected ? selected.dataset.code || '' : '';
            });
        },

        async calculateDeduction() {
            if (!this.amount || parseFloat(this.amount) <= 0) {
                this.deductionCalculated = false;
                return;
            }

            try {
                const response = await fetch('{{ route("tenant.accounting.payouts.calculate-deduction", ["tenant" => $tenant->slug]) }}?amount=' + this.amount);
                const data = await response.json();

                if (data.success) {
                    this.requestedDisplay = data.requested_amount;
                    this.deductionDisplay = data.deduction_amount;
                    this.netDisplay = data.net_amount;
                    this.deductionLabel = data.deduction_description || 'Processing Fee';
                    this.deductionCalculated = true;
                }
            } catch (e) {
                console.error('Deduction calculation failed:', e);
            }
        },

        submitForm(event) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            event.target.submit();
        }
    };
}
</script>
@endpush
@endsection
