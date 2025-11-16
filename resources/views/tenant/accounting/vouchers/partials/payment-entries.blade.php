<div class="bg-white shadow-lg rounded-xl border border-gray-200" x-data="paymentVoucherEntries()">
    <div class="p-6">
        {{-- Bank Account Section (First Entry - Debit) --}}
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Bank Account</h3>
                    <p class="text-sm text-gray-500">Credit entry - Money going out</p>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-4 items-start p-5 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 shadow-sm">
            {{-- Bank Account Dropdown --}}
            <div class="col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Bank/Cash Account <span class="text-red-500">*</span>
                </label>
                <select
                    x-model="bankEntry.ledger_account_id"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
                    <option value="">Select Bank/Cash Account</option>
                    @foreach($ledgerAccounts->where('account_type', 'asset')->concat($ledgerAccounts->where('account_type', 'current asset')) as $account)
                        @if(stripos($account->name, 'bank') !== false || stripos($account->name, 'cash') !== false)
                            <option value="{{ $account->id }}">
                                {{ $account->name }} ({{ $account->code }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Particulars --}}
            <div class="col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Particulars
                </label>
                <input
                    type="text"
                    x-model="bankEntry.particulars"
                    @keyup="syncParticulars()"
                    placeholder="Payment description"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
            </div>

            {{-- Credit Amount (Auto-calculated, Read-only) --}}
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Credit Amount (Auto)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                    <input
                        type="text"
                        :value="formatNumber(totalPaymentAmount)"
                        readonly
                        class="w-full pl-8 rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm font-semibold text-gray-700 cursor-not-allowed"
                    >
                </div>
            </div>

            {{-- Hidden Debit Amount (always 0 for bank in payment voucher) --}}
            <input type="hidden" :value="0">

                {{-- Type Badge --}}
                <div class="col-span-1 flex items-end justify-center pb-2">
                    <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg shadow-sm">Cr.</span>
                </div>
            </div>

            {{-- Hidden inputs for bank entry submission --}}
            <input type="hidden" name="entries[0][ledger_account_id]" :value="bankEntry.ledger_account_id">
            <input type="hidden" name="entries[0][particulars]" :value="bankEntry.particulars">
            <input type="hidden" name="entries[0][debit_amount]" value="0">
            <input type="hidden" name="entries[0][credit_amount]" :value="totalPaymentAmount">
        </div>

        {{-- Payment Entries Section (Multiple Entries - Credit) --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Payment Entries</h3>
                        <p class="text-sm text-gray-500">Debit entries - Accounts being paid</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="addEntry()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all"
                >
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Entry
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(entry, index) in paymentEntries" :key="index">
                    <div class="grid grid-cols-12 gap-4 items-start p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 hover:border-blue-400 transition-all shadow-sm hover:shadow-md">
                    {{-- Ledger Account Searchable --}}
                    <div class="col-span-3" x-data="{ search: '', showDropdown: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ledger Account <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="search"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                placeholder="Search account..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                            <div x-show="showDropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                @foreach($ledgerAccounts as $account)
                                    <div
                                        x-show="'{{ strtolower($account->name . ' ' . $account->code) }}'.includes(search.toLowerCase())"
                                        @click="entry.ledger_account_id = {{ $account->id }}; search = '{{ $account->name }} ({{ $account->code }})'; showDropdown = false"
                                        class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm"
                                    >
                                        {{ $account->name }} ({{ $account->code }})
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Particulars --}}
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Particulars
                        </label>
                        <input
                            type="text"
                            x-model="entry.particulars"
                            placeholder="Entry description"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                    </div>

                    {{-- Debit Amount (User Input) --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Debit Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                            <input
                                type="number"
                                step="0.01"
                                x-model.number="entry.debit_amount"
                                @input="calculateTotal()"
                                required
                                placeholder="0.00"
                                class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            >
                        </div>
                    </div>

                    {{-- Document Upload --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Document
                        </label>
                        <div class="relative">
                            <input
                                type="file"
                                :name="`entries[${index + 1}][document]`"
                                accept=".jpg,.jpeg,.png,.pdf"
                                @change="handleFileChange($event, index)"
                                class="block w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-500 file:to-blue-600 file:text-white hover:file:from-blue-600 hover:file:to-blue-700 file:cursor-pointer file:shadow-sm hover:file:shadow-md file:transition-all"
                            >
                        </div>
                        <div x-show="entry.fileName" class="mt-2 text-xs text-green-600 flex items-center bg-green-50 px-2 py-1 rounded">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="entry.fileName"></span>
                        </div>
                    </div>

                    {{-- Hidden Credit Amount (always 0 for payment entries) --}}
                    <input type="hidden" :value="0">

                        {{-- Type Badge & Remove Button --}}
                        <div class="col-span-2 flex items-end justify-between pb-2">
                            <span class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg shadow-sm">Dr.</span>
                            <button
                                type="button"
                                @click="removeEntry(index)"
                                x-show="paymentEntries.length > 1"
                                class="p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all"
                                title="Remove entry"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Hidden inputs for payment entry submission --}}
                        <input type="hidden" :name="`entries[${index + 1}][ledger_account_id]`" :value="entry.ledger_account_id">
                        <input type="hidden" :name="`entries[${index + 1}][particulars]`" :value="entry.particulars">
                        <input type="hidden" :name="`entries[${index + 1}][debit_amount]`" :value="entry.debit_amount">
                        <input type="hidden" :name="`entries[${index + 1}][credit_amount]`" value="0">
                    </div>
                </template>
            </div>
        </div>

        {{-- Totals Section --}}
        <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-xl p-6 mb-6 border-2 border-indigo-200 shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Total Payment Amount --}}
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-600">Total Payment Amount</span>
                    </div>
                    <div class="text-3xl font-bold text-indigo-600">
                        ₦<span x-text="formatNumber(totalPaymentAmount)"></span>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-sm font-semibold text-gray-700 mb-3">Transaction Summary</div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 bg-red-50 rounded-lg">
                            <span class="text-sm text-gray-700">Bank Account</span>
                            <div class="flex items-center">
                                <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded mr-2">Cr</span>
                                <span class="font-semibold text-gray-900">₦<span x-text="formatNumber(totalPaymentAmount)"></span></span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                            <span class="text-sm text-gray-700">Payment Entries</span>
                            <div class="flex items-center">
                                <span class="px-2 py-1 bg-green-600 text-white text-xs font-bold rounded mr-2">Dr</span>
                                <span class="font-semibold text-gray-900">₦<span x-text="formatNumber(totalPaymentAmount)"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Section --}}
        <div class="flex justify-end space-x-3 pt-6 border-t-2 border-gray-200">
            <a
                href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
                class="inline-flex items-center justify-center rounded-lg border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
            <button
                type="submit"
                name="action"
                value="save"
                @click="setTimeout(() => isSubmitting = true, 0)"
                :disabled="isSubmitting"
                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                class="inline-flex items-center justify-center rounded-lg border-2 border-blue-600 bg-white px-6 py-3 text-sm font-semibold text-blue-600 shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Save as Draft
            </button>
            <button
                type="submit"
                name="action"
                value="save_and_post"
                @click="setTimeout(() => isSubmitting = true, 0)"
                :disabled="isSubmitting"
                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                class="inline-flex items-center justify-center rounded-lg border border-transparent bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Save & Post
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function paymentVoucherEntries() {
    return {
        bankEntry: {
            ledger_account_id: '',
            particulars: ''
        },
        paymentEntries: [
            {
                ledger_account_id: '',
                particulars: '',
                debit_amount: 0,
                fileName: '',
                search: ''
            }
        ],
        totalPaymentAmount: 0,
        isSubmitting: false,
        lastBankParticulars: '',

        init() {
            this.calculateTotal();
        },

        addEntry() {
            this.paymentEntries.push({
                ledger_account_id: '',
                particulars: this.bankEntry.particulars,
                debit_amount: 0,
                fileName: '',
                search: ''
            });
        },

        syncParticulars() {
            this.paymentEntries.forEach(entry => {
                if (!entry.particulars || entry.particulars === this.lastBankParticulars) {
                    entry.particulars = this.bankEntry.particulars;
                }
            });
            this.lastBankParticulars = this.bankEntry.particulars;
        },

        removeEntry(index) {
            if (this.paymentEntries.length > 1) {
                this.paymentEntries.splice(index, 1);
                this.calculateTotal();
            }
        },

        handleFileChange(event, index) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    event.target.value = '';
                    this.paymentEntries[index].fileName = '';
                    return;
                }
                this.paymentEntries[index].fileName = file.name;
            } else {
                this.paymentEntries[index].fileName = '';
            }
        },

        calculateTotal() {
            this.totalPaymentAmount = this.paymentEntries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.debit_amount) || 0);
            }, 0);
        },

        formatNumber(value) {
            return parseFloat(value || 0).toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}
</script>
@endpush

