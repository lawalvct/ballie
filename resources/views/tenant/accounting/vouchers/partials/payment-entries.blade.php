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
                <div class="flex gap-2">
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
                    <button
                        type="button"
                        @click="showBulkUploadModal = true"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Bulk Upload
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <template x-for="(entry, index) in paymentEntries" :key="index">
                    <div class="grid grid-cols-12 gap-4 items-start p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200 hover:border-blue-400 transition-all shadow-sm hover:shadow-md">
                    {{-- Ledger Account Dropdown --}}
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ledger Account <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="entry.ledger_account_id"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="">Select Account</option>
                            @foreach($ledgerAccounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }} ({{ $account->code }})
                                </option>
                            @endforeach
                        </select>
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
                :disabled="isSubmitting"
                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                class="inline-flex items-center justify-center rounded-lg border-2 border-blue-600 bg-white px-6 py-3 text-sm font-semibold text-blue-600 shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all"
            >
                <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Saving...' : 'Save as Draft'"></span>
            </button>
            <button
                type="submit"
                name="action"
                value="save_and_post"
                :disabled="isSubmitting"
                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                class="inline-flex items-center justify-center rounded-lg border border-transparent bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
            >
                <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Posting...' : 'Save & Post'"></span>
            </button>
        </div>
    </div>

    {{-- Bulk Upload Modal --}}
    <div x-show="showBulkUploadModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true"
         @keydown.escape.window="showBulkUploadModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showBulkUploadModal = false"></div>

            {{-- Modal panel --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-bold text-gray-900">
                            Bulk Upload Payment Entries
                        </h3>
                        <button @click="showBulkUploadModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        {{-- Instructions --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">How to use bulk upload</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ol class="list-decimal list-inside space-y-1">
                                            <li>Select the Bank/Cash account that will be credited</li>
                                            <li>Download the template and fill in your payment entries</li>
                                            <li>Upload the completed file</li>
                                            <li>Review the entries and submit</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Download Template Button --}}
                        <div class="flex justify-center">
                            <a :href="`{{ route('tenant.accounting.vouchers.bulk-payment-template', $tenant->slug) }}`"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 shadow-sm">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Template
                            </a>
                        </div>

                        {{-- Upload Form --}}
                        <div class="space-y-4">
                            {{-- Bank Account Selection --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Bank/Cash Account (Will be Credited) <span class="text-red-500">*</span>
                                </label>
                                <select x-model="bulkUpload.bankAccountId"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Bank/Cash Account</option>
                                    @foreach($ledgerAccounts->where('account_type', 'asset')->concat($ledgerAccounts->where('account_type', 'current asset')) as $account)
                                        @if(stripos($account->name, 'bank') !== false || stripos($account->name, 'cash') !== false)
                                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->code }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- File Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Upload File <span class="text-red-500">*</span>
                                </label>
                                <input type="file"
                                       @change="handleBulkFileChange($event)"
                                       accept=".xlsx,.xls,.csv"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                                <p class="mt-1 text-xs text-gray-500">Excel (.xlsx, .xls) or CSV files only (max 10MB)</p>
                            </div>

                            {{-- Preview Section --}}
                            <div x-show="bulkUpload.previewData.length > 0" class="mt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Preview: <span x-text="bulkUpload.previewData.length"></span> entries found</h4>
                                <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ledger</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <template x-for="(row, index) in bulkUpload.previewData" :key="index">
                                                <tr>
                                                    <td class="px-3 py-2 text-sm text-gray-900" x-text="row.date"></td>
                                                    <td class="px-3 py-2 text-sm text-gray-900" x-text="row.ledger"></td>
                                                    <td class="px-3 py-2 text-sm text-gray-900" x-text="row.description"></td>
                                                    <td class="px-3 py-2 text-sm text-gray-900 text-right" x-text="formatNumber(row.amount)"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-3 py-2 text-sm font-semibold text-gray-900 text-right">Total:</td>
                                                <td class="px-3 py-2 text-sm font-semibold text-gray-900 text-right">₦<span x-text="formatNumber(bulkUpload.totalAmount)"></span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            {{-- Error Display --}}
                            <div x-show="bulkUpload.errors.length > 0" class="bg-red-50 border-l-4 border-red-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Validation Errors</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <template x-for="error in bulkUpload.errors" :key="error">
                                                    <li x-text="error"></li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            @click="submitBulkUpload()"
                            :disabled="bulkUpload.uploading || !bulkUpload.file || !bulkUpload.bankAccountId || bulkUpload.errors.length > 0"
                            :class="(bulkUpload.uploading || !bulkUpload.file || !bulkUpload.bankAccountId || bulkUpload.errors.length > 0) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span x-show="!bulkUpload.uploading">Upload & Create Voucher</span>
                        <span x-show="bulkUpload.uploading">
                            <svg class="animate-spin h-5 w-5 text-white inline mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </span>
                    </button>
                    <button type="button"
                            @click="showBulkUploadModal = false; resetBulkUpload()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
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
        showBulkUploadModal: false,
        bulkUpload: {
            bankAccountId: '',
            file: null,
            previewData: [],
            totalAmount: 0,
            uploading: false,
            errors: []
        },

        init() {
            this.calculateTotal();

            // Listen for form submission
            const form = this.$el.closest('form');
            if (form) {
                form.addEventListener('submit', (e) => {
                    // Validate before allowing submission
                    if (!this.validateForm()) {
                        e.preventDefault();
                        return false;
                    }
                    this.isSubmitting = true;
                });
            }
        },

        validateForm() {
            // Check if bank account is selected
            if (!this.bankEntry.ledger_account_id) {
                alert('Please select a Bank/Cash account');
                return false;
            }

            // Check if at least one payment entry has values
            const hasValidEntry = this.paymentEntries.some(entry =>
                entry.ledger_account_id && entry.debit_amount > 0
            );

            if (!hasValidEntry) {
                alert('Please add at least one payment entry with an account and amount');
                return false;
            }

            // Check if total is greater than zero
            if (this.totalPaymentAmount <= 0) {
                alert('Total payment amount must be greater than zero');
                return false;
            }

            return true;
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

        handleBulkFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    event.target.value = '';
                    return;
                }

                // Validate file type
                const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel',
                                    'text/csv'];
                if (!validTypes.includes(file.type)) {
                    alert('Please upload a valid Excel or CSV file');
                    event.target.value = '';
                    return;
                }

                this.bulkUpload.file = file;
                this.previewBulkFile(file);
            } else {
                this.resetBulkUpload();
            }
        },

        previewBulkFile(file) {
            // For now, just store the file. Server will handle parsing.
            // In production, you could use SheetJS to parse client-side for preview
            this.bulkUpload.previewData = [];
            this.bulkUpload.errors = [];
        },

        async submitBulkUpload() {
            if (!this.bulkUpload.file || !this.bulkUpload.bankAccountId) {
                alert('Please select a bank account and upload a file');
                return;
            }

            this.bulkUpload.uploading = true;
            this.bulkUpload.errors = [];

            const formData = new FormData();
            formData.append('file', this.bulkUpload.file);
            formData.append('bank_account_id', this.bulkUpload.bankAccountId);
            formData.append('voucher_date', document.querySelector('[name="voucher_date"]').value || new Date().toISOString().split('T')[0]);
            formData.append('narration', document.querySelector('[name="narration"]')?.value || '');

            try {
                const response = await fetch(`{{ route('tenant.accounting.vouchers.upload-bulk-payments', $tenant->slug) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Success - redirect to the voucher
                    alert(`Successfully uploaded ${data.message}`);
                    window.location.href = data.redirect_url;
                } else {
                    // Show errors
                    if (data.errors) {
                        // Handle different error formats
                        if (Array.isArray(data.errors)) {
                            // Check if errors are objects with row and message
                            this.bulkUpload.errors = data.errors.map(error => {
                                if (typeof error === 'object' && error.row && error.message) {
                                    return `Row ${error.row}: ${error.message}`;
                                } else if (typeof error === 'string') {
                                    return error;
                                } else {
                                    return String(error);
                                }
                            });
                        } else if (typeof data.errors === 'object') {
                            // Object with error arrays (Laravel validation format)
                            const errorMessages = [];
                            for (const field in data.errors) {
                                if (Array.isArray(data.errors[field])) {
                                    errorMessages.push(...data.errors[field]);
                                } else {
                                    errorMessages.push(String(data.errors[field]));
                                }
                            }
                            this.bulkUpload.errors = errorMessages;
                        } else {
                            this.bulkUpload.errors = [String(data.errors)];
                        }
                    } else if (data.message) {
                        this.bulkUpload.errors = [data.message];
                    } else {
                        this.bulkUpload.errors = ['An error occurred during upload'];
                    }
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.bulkUpload.errors = ['Network error: ' + error.message];
            } finally {
                this.bulkUpload.uploading = false;
            }
        },

        resetBulkUpload() {
            this.bulkUpload = {
                bankAccountId: '',
                file: null,
                previewData: [],
                totalAmount: 0,
                uploading: false,
                errors: []
            };
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

