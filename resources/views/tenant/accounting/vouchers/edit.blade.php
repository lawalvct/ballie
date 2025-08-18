@extends('layouts.tenant')

@section('title', 'Edit Voucher ' . $voucher->voucher_number . ' - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="voucherForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Edit Voucher {{ $voucher->voucher_number }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $voucher->voucherType->name }} • Created {{ $voucher->created_at->format('M d, Y') }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Voucher
            </a>
        </div>
    </div>

    @if($voucher->status === 'posted')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Posted Voucher Warning
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>This voucher has been posted and affects the general ledger. Changes should be made carefully. Consider unposting the voucher first if major changes are needed.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.accounting.vouchers.update', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Voucher Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Voucher Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Voucher Type -->
                    <div>
                        <label for="voucher_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Type <span class="text-red-500">*</span>
                        </label>
                        <select name="voucher_type_id"
                                id="voucher_type_id"
                                x-model="voucherTypeId"
                                @change="updateVoucherType()"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg @error('voucher_type_id') border-red-300 @enderror"
                                required>
                            <option value="">Select Voucher Type</option>
                            @foreach($voucherTypes as $type)
                                <option value="{{ $type->id }}"
                                        {{ (old('voucher_type_id', $voucher->voucher_type_id) == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('voucher_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Voucher Date -->
                    <div>
                        <label for="voucher_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="voucher_date"
                               id="voucher_date"
                               value="{{ old('voucher_date', $voucher->voucher_date->format('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('voucher_date') border-red-300 @enderror"
                               required>
                        @error('voucher_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Reference Number
                        </label>
                        <input type="text"
                               name="reference_number"
                               id="reference_number"
                               value="{{ old('reference_number', $voucher->reference_number) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('reference_number') border-red-300 @enderror"
                               placeholder="Optional reference">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Voucher Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Number
                        </label>
                        <div class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                            {{ $voucher->voucher_number }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Cannot be changed</p>
                    </div>
                </div>

                <!-- Narration -->
                <div class="mt-6">
                    <label for="narration" class="block text-sm font-medium text-gray-700 mb-2">
                        Narration
                    </label>
                    <textarea name="narration"
                              id="narration"
                              rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('narration') border-red-300 @enderror"
                              placeholder="Enter voucher description or narration">{{ old('narration', $voucher->narration) }}</textarea>
                    @error('narration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Voucher Entries -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Voucher Entries</h3>
                    <button type="button"
                            @click="addEntry()"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Entry
                    </button>
                </div>
            </div>
            <div class="p-6">
                @error('entries')
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Entries Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ledger Account <span class="text-red-500">*</span>
                                </th>
                                <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Particulars
                                </th>
                                <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Debit Amount
                                </th>
                                <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Credit Amount
                                </th>
                                <th class="text-center py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(entry, index) in entries" :key="index">
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-2">
                                        <select :name="`entries[${index}][ledger_account_id]`"
                                                x-model="entry.ledger_account_id"
                                                @change="updateEntryAccount(index)"
                                                class="block w-full pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md"
                                                required>
                                            <option value="">Select Account</option>
                                            @foreach($ledgerAccounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->name }} ({{ $account->accountGroup->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" :name="`entries[${index}][id]`" x-model="entry.id">
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="text"
                                               :name="`entries[${index}][particulars]`"
                                               x-model="entry.particulars"
                                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                               placeholder="Entry description">
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="number"
                                               :name="`entries[${index}][debit_amount]`"
                                               x-model="entry.debit_amount"
                                               @input="updateTotals(); clearCredit(index)"
                                               step="0.01"
                                               min="0"
                                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                               placeholder="0.00">
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="number"
                                               :name="`entries[${index}][credit_amount]`"
                                               x-model="entry.credit_amount"
                                               @input="updateTotals(); clearDebit(index)"
                                               step="0.01"
                                               min="0"
                                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                               placeholder="0.00">
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <button type="button"
                                                @click="removeEntry(index)"
                                                x-show="entries.length > 2"
                                                class="text-red-600 hover:text-red-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 bg-gray-50">
                                <td colspan="2" class="py-3 px-2 text-sm font-medium text-gray-900">
                                    Total
                                </td>
                                <td class="py-3 px-2 text-right text-sm font-medium text-gray-900">
                                    ₦<span x-text="formatNumber(totalDebits)"></span>
                                </td>
                                <td class="py-3 px-2 text-right text-sm font-medium text-gray-900">
                                    ₦<span x-text="formatNumber(totalCredits)"></span>
                                </td>
                                <td class="py-3 px-2"></td>
                            </tr>
                            <tr x-show="!isBalanced" class="bg-red-50">
                                <td colspan="5" class="py-2 px-2 text-center text-sm text-red-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Voucher is not balanced. Difference: ₦<span x-text="formatNumber(Math.abs(totalDebits - totalCredits))"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    <span x-show="isBalanced" class="text-green-600">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Voucher is balanced
                    </span>
                    <span x-show="!isBalanced" class="text-red-600">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Voucher must be balanced to save
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('tenant.accounting.vouchers.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit"
                        :disabled="!isBalanced || entries.length < 2"
                        :class="{ 'opacity-50 cursor-not-allowed': !isBalanced || entries.length < 2 }"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Voucher
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function voucherForm() {
    return {
        voucherTypeId: '{{ old('voucher_type_id', $voucher->voucher_type_id) }}',
        entries: @json(old('entries', $entriesData ?? [])),
        totalDebits: 0,
        totalCredits: 0,
        voucherTypes: @json($voucherTypes->keyBy('id')),

        get isBalanced() {
            return Math.abs(this.totalDebits - this.totalCredits) < 0.01 && this.totalDebits > 0;
        },

        init() {
            this.updateTotals();
        },

        addEntry() {
            this.entries.push({
                id: null,
                ledger_account_id: '',
                particulars: '',
                debit_amount: '',
                credit_amount: ''
            });
        },

        removeEntry(index) {
            if (this.entries.length > 2) {
                this.entries.splice(index, 1);
                this.updateTotals();
            }
        },

        clearDebit(index) {
            if (this.entries[index].credit_amount) {
                this.entries[index].debit_amount = '';
            }
        },

        clearCredit(index) {
            if (this.entries[index].debit_amount) {
                this.entries[index].credit_amount = '';
            }
        },

        updateTotals() {
            this.totalDebits = this.entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.debit_amount) || 0);
            }, 0);

            this.totalCredits = this.entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.credit_amount) || 0);
            }, 0);
        },

        updateVoucherType() {
            // This method can be used if voucher type change is allowed
            // Currently disabled in edit mode for data integrity
        },

        updateEntryAccount(index) {
            // Auto-fill particulars based on account selection if empty
            if (!this.entries[index].particulars && this.entries[index].ledger_account_id) {
                const accountSelect = document.querySelector('select[name="entries[' + index + '][ledger_account_id]"]');
                const selectedOption = accountSelect.options[accountSelect.selectedIndex];
                if (selectedOption && selectedOption.text) {
                    this.entries[index].particulars = 'Being ' + selectedOption.text.split(' (')[0];
                }
            }
        },

        formatNumber(num) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num || 0);
        }
    }
}
</script>
@endpush
@endsection
