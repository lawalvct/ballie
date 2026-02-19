<div class="bg-white shadow-lg rounded-xl border border-gray-200" x-data="receiptVoucherEntries()">
    <div class="p-6">
        {{-- Receipt Entries Section (Multiple Entries - Credit) --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Receipt Entries</h3>
                        <p class="text-sm text-gray-500">Credit entries - Money received from (reduces receivables)</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="addEntry()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm hover:shadow-md transition-all"
                >
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Entry
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(entry, index) in receiptEntries" :key="index">
                    <div class="grid grid-cols-12 gap-4 items-start p-5 bg-gradient-to-br from-red-50 to-rose-50 rounded-xl border-2 border-red-200 hover:border-red-400 transition-all shadow-sm hover:shadow-md">
                    {{-- Ledger Account Searchable Dropdown --}}
                    <div class="col-span-4 relative" @click.away="entry.showLedgerDropdown = false">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ledger Account <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            x-model="entry.ledger_search"
                            @focus="entry.showLedgerDropdown = true"
                            @input="entry.showLedgerDropdown = true; entry.ledger_account_id = ''"
                            @keydown.escape.prevent="entry.showLedgerDropdown = false"
                            placeholder="Search account"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            required
                        >
                        <div
                            x-show="entry.showLedgerDropdown"
                            x-transition
                            class="absolute z-20 mt-1 w-full max-h-56 overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg"
                        >
                            <template x-for="account in getFilteredLedgerAccounts(entry)" :key="account.id">
                                <button
                                    type="button"
                                    @click="selectLedgerAccount(entry, account)"
                                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-green-50"
                                >
                                    <span x-text="account.display"></span>
                                </button>
                            </template>
                            <div x-show="getFilteredLedgerAccounts(entry).length === 0" class="px-3 py-2 text-sm text-gray-500">
                                No matching accounts
                            </div>
                        </div>
                    </div>

                    {{-- Particulars --}}
                    <div class="col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Particulars
                        </label>
                        <input
                            type="text"
                            x-model="entry.particulars"
                            placeholder="Entry description"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                    </div>

                    {{-- Credit Amount (User Input) --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Credit Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                            <input
                                type="number"
                                step="0.01"
                                x-model.number="entry.credit_amount"
                                @input="calculateTotal()"
                                required
                                placeholder="0.00"
                                class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            >
                        </div>
                    </div>

                    {{-- Hidden Debit Amount (always 0 for receipt entries) --}}
                    <input type="hidden" :value="0">

                        {{-- Type Badge & Remove Button --}}
                        <div class="col-span-2 flex items-end justify-between pb-2">
                            <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg shadow-sm">Cr.</span>
                            <button
                                type="button"
                                @click="removeEntry(index)"
                                x-show="receiptEntries.length > 1"
                                class="p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all"
                                title="Remove entry"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Hidden inputs for receipt entry submission --}}
                        <input type="hidden" :name="`entries[${index}][ledger_account_id]`" :value="entry.ledger_account_id">
                        <input type="hidden" :name="`entries[${index}][particulars]`" :value="entry.particulars">
                        <input type="hidden" :name="`entries[${index}][debit_amount]`" value="0">
                        <input type="hidden" :name="`entries[${index}][credit_amount]`" :value="entry.credit_amount">
                    </div>
                </template>
            </div>
        </div>

        {{-- Outstanding Invoices Section (Optional - appears when a customer ledger is selected) --}}
        <div x-show="outstandingInvoices.length > 0" x-transition class="mb-8">
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border-2 border-amber-200 shadow-sm">
                <div class="px-5 py-4 border-b border-amber-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Outstanding Invoices</h3>
                                <p class="text-sm text-gray-500">
                                    <span x-text="invoiceCustomerName"></span> &mdash;
                                    Select invoices to link this payment (optional)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-medium text-amber-700 bg-amber-100 px-2 py-1 rounded-full"
                                  x-text="outstandingInvoices.length + ' invoice(s)'"></span>
                            <button type="button" @click="showInvoicePanel = !showInvoicePanel"
                                    class="text-amber-600 hover:text-amber-800 transition-colors">
                                <svg :class="showInvoicePanel ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="showInvoicePanel" x-transition class="p-5">
                    {{-- Loading indicator --}}
                    <div x-show="loadingInvoices" class="flex items-center justify-center py-6">
                        <svg class="animate-spin h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-500">Loading invoices...</span>
                    </div>

                    {{-- Invoice table --}}
                    <div x-show="!loadingInvoices">
                        <div class="overflow-x-auto rounded-lg border border-amber-100">
                            <table class="min-w-full divide-y divide-amber-100">
                                <thead class="bg-amber-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            <input type="checkbox" @change="toggleAllInvoices($event.target.checked)"
                                                   :checked="selectedInvoices.length === outstandingInvoices.length && outstandingInvoices.length > 0"
                                                   class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice #</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Paid</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance Due</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Pay Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <template x-for="(inv, idx) in outstandingInvoices" :key="inv.id">
                                        <tr :class="isInvoiceSelected(inv.id) ? 'bg-amber-50' : 'hover:bg-gray-50'" class="transition-colors">
                                            <td class="px-4 py-3">
                                                <input type="checkbox"
                                                       :checked="isInvoiceSelected(inv.id)"
                                                       @change="toggleInvoice(inv, $event.target.checked)"
                                                       class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-semibold text-gray-900" x-text="inv.reference"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm text-gray-600" x-text="inv.voucher_date_formatted"></span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-medium text-gray-900">₦<span x-text="formatNumber(inv.total_amount)"></span></span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm text-green-600">₦<span x-text="formatNumber(inv.total_paid)"></span></span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-semibold text-red-600">₦<span x-text="formatNumber(inv.balance_due)"></span></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span :class="inv.status === 'Partially Paid' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                                                      class="px-2 py-1 text-xs font-medium rounded-full"
                                                      x-text="inv.status"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="relative" x-show="isInvoiceSelected(inv.id)">
                                                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-500 text-xs">₦</span>
                                                    <input type="number" step="0.01" min="0.01"
                                                           :max="inv.balance_due"
                                                           :value="getInvoicePayAmount(inv.id)"
                                                           @input="updateInvoicePayAmount(inv.id, $event.target.value)"
                                                           class="w-28 pl-6 pr-2 py-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                                </div>
                                                <span x-show="!isInvoiceSelected(inv.id)" class="text-sm text-gray-400">-</span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-amber-50">
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">
                                            Selected Total:
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-amber-700">
                                            ₦<span x-text="formatNumber(selectedInvoicesTotal)"></span>
                                        </td>
                                        <td></td>
                                        <td class="px-4 py-3 text-right text-sm font-bold text-green-700">
                                            ₦<span x-text="formatNumber(selectedInvoicesPayTotal)"></span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Apply to entries button --}}
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-xs text-gray-500">
                                <svg class="w-4 h-4 inline text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                This is optional. Selected invoices will be linked to this receipt for tracking.
                            </p>
                            <button type="button"
                                    @click="applyInvoicesToEntries()"
                                    x-show="selectedInvoices.length > 0"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-sm transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Apply to Receipt Entries
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden inputs for linked invoices --}}
        <template x-for="(sel, si) in selectedInvoices" :key="'linked-' + sel.invoice_id">
            <div>
                <input type="hidden" :name="'linked_invoices[' + si + '][invoice_id]'" :value="sel.invoice_id">
                <input type="hidden" :name="'linked_invoices[' + si + '][invoice_reference]'" :value="sel.reference">
                <input type="hidden" :name="'linked_invoices[' + si + '][amount]'" :value="sel.pay_amount">
            </div>
        </template>

        {{-- Bank Account Section (Last Entry - Debit) --}}
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Bank Account</h3>
                    <p class="text-sm text-gray-500">Debit entry - Money received into bank (increases cash)</p>
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
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
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
                    placeholder="Receipt description"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                >
            </div>

            {{-- Debit Amount (Auto-calculated, Read-only) --}}
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Debit Amount (Auto)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                    <input
                        type="text"
                        :value="formatNumber(totalReceiptAmount)"
                        readonly
                        class="w-full pl-8 rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm font-semibold text-gray-700 cursor-not-allowed"
                    >
                </div>
            </div>

            {{-- Hidden Credit Amount (always 0 for bank in receipt voucher) --}}
            <input type="hidden" :value="0">

                {{-- Type Badge --}}
                <div class="col-span-1 flex items-end justify-center pb-2">
                    <span class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg shadow-sm">Dr.</span>
                </div>
            </div>

            {{-- Hidden inputs for bank entry submission - use last index after receipt entries --}}
            <template x-for="(entry, index) in receiptEntries" :key="`bank-${index}`" x-show="false">
                <span></span>
            </template>
            <input type="hidden" :name="`entries[${receiptEntries.length}][ledger_account_id]`" :value="bankEntry.ledger_account_id">
            <input type="hidden" :name="`entries[${receiptEntries.length}][particulars]`" :value="bankEntry.particulars">
            <input type="hidden" :name="`entries[${receiptEntries.length}][debit_amount]`" :value="totalReceiptAmount">
            <input type="hidden" :name="`entries[${receiptEntries.length}][credit_amount]`" value="0">
        </div>

        {{-- Totals Section --}}
        <div class="bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 rounded-xl p-6 mb-6 border-2 border-emerald-200 shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Total Receipt Amount --}}
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-600">Total Receipt Amount</span>
                    </div>
                    <div class="text-3xl font-bold text-emerald-600">
                        ₦<span x-text="formatNumber(totalReceiptAmount)"></span>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-sm font-semibold text-gray-700 mb-3">Transaction Summary</div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-2 bg-red-50 rounded-lg">
                            <span class="text-sm text-gray-700">Receipt From (Customer/Party)</span>
                            <div class="flex items-center">
                                <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded mr-2">Cr</span>
                                <span class="font-semibold text-gray-900">₦<span x-text="formatNumber(totalReceiptAmount)"></span></span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                            <span class="text-sm text-gray-700">Bank Account (Money In)</span>
                            <div class="flex items-center">
                                <span class="px-2 py-1 bg-green-600 text-white text-xs font-bold rounded mr-2">Dr</span>
                                <span class="font-semibold text-gray-900">₦<span x-text="formatNumber(totalReceiptAmount)"></span></span>
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
                class="inline-flex items-center justify-center rounded-lg border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
            <button
                type="button"
                @click="submitForm('save')"
                :disabled="isSubmitting"
                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                class="inline-flex items-center justify-center rounded-lg border-2 border-green-600 bg-white px-6 py-3 text-sm font-semibold text-green-600 shadow-sm hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all"
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
                type="button"
                @click="submitForm('save_and_post')"
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
            <button
                type="button"
                @click="submitForm('save_and_post_return')"
                :disabled="isSubmitting"
                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                class="inline-flex items-center justify-center rounded-lg border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
            >
                <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <svg x-show="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Processing...' : 'Save, Post & Create Another'"></span>
            </button>
        </div>
    </div>
</div>

@php
    $receiptLedgerAccountsJson = $ledgerAccounts
        ->filter(function ($account) {
            $groupCode = $account->accountGroup?->code;
            $name = strtolower($account->name ?? '');
            return in_array($groupCode, ['AR', 'AP'])
                && !str_contains($name, 'bank')
                && !str_contains($name, 'cash');
        })
        ->map(function ($account) {
            return [
                'id' => $account->id,
                'name' => $account->name,
                'code' => $account->code,
                'display' => $account->name . ' (' . $account->code . ')',
            ];
        })
        ->values();
@endphp

@push('scripts')
<script>
function receiptVoucherEntries() {
    return {
        bankEntry: {
            ledger_account_id: '',
            particulars: ''
        },
        ledgerAccounts: @json($receiptLedgerAccountsJson),
        receiptEntries: [
            {
                ledger_account_id: '',
                ledger_search: '',
                showLedgerDropdown: false,
                particulars: '',
                credit_amount: 0
            }
        ],
        totalReceiptAmount: 0,
        isSubmitting: false,

        // Invoice linking (optional feature)
        outstandingInvoices: [],
        selectedInvoices: [], // [{invoice_id, reference, balance_due, pay_amount}]
        selectedInvoicesTotal: 0,
        selectedInvoicesPayTotal: 0,
        showInvoicePanel: true,
        loadingInvoices: false,
        invoiceCustomerName: '',
        lastFetchedLedgerId: null,

        init() {
            this.calculateTotal();

            // Watch for ledger account changes on the first entry to fetch invoices
            this.$watch('receiptEntries', (entries) => {
                if (entries.length > 0 && entries[0].ledger_account_id) {
                    const firstLedgerId = entries[0].ledger_account_id;
                    if (firstLedgerId !== this.lastFetchedLedgerId) {
                        this.lastFetchedLedgerId = firstLedgerId;
                        this.fetchCustomerInvoices(firstLedgerId);
                    }
                } else if (entries.length > 0 && !entries[0].ledger_account_id) {
                    // Ledger cleared
                    this.outstandingInvoices = [];
                    this.selectedInvoices = [];
                    this.selectedInvoicesTotal = 0;
                    this.selectedInvoicesPayTotal = 0;
                    this.lastFetchedLedgerId = null;
                    this.invoiceCustomerName = '';
                }
            }, { deep: true });
        },

        async fetchCustomerInvoices(ledgerAccountId) {
            this.loadingInvoices = true;
            this.outstandingInvoices = [];
            this.selectedInvoices = [];
            this.selectedInvoicesTotal = 0;
            this.selectedInvoicesPayTotal = 0;

            try {
                const url = `{{ route('tenant.accounting.vouchers.customer-invoices', ['tenant' => $tenant->slug, 'ledgerAccount' => '__ID__']) }}`.replace('__ID__', ledgerAccountId);
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.invoices.length > 0) {
                        this.outstandingInvoices = data.invoices;
                        this.invoiceCustomerName = data.customer_name || '';
                    } else {
                        this.outstandingInvoices = [];
                    }
                }
            } catch (error) {
                console.error('Error fetching customer invoices:', error);
                this.outstandingInvoices = [];
            } finally {
                this.loadingInvoices = false;
            }
        },

        isInvoiceSelected(invoiceId) {
            return this.selectedInvoices.some(s => s.invoice_id === invoiceId);
        },

        getInvoicePayAmount(invoiceId) {
            const sel = this.selectedInvoices.find(s => s.invoice_id === invoiceId);
            return sel ? sel.pay_amount : 0;
        },

        toggleInvoice(inv, checked) {
            if (checked) {
                this.selectedInvoices.push({
                    invoice_id: inv.id,
                    reference: inv.reference,
                    balance_due: inv.balance_due,
                    pay_amount: inv.balance_due,
                });
            } else {
                this.selectedInvoices = this.selectedInvoices.filter(s => s.invoice_id !== inv.id);
            }
            this.recalcInvoiceTotals();
        },

        toggleAllInvoices(checked) {
            if (checked) {
                this.selectedInvoices = this.outstandingInvoices.map(inv => ({
                    invoice_id: inv.id,
                    reference: inv.reference,
                    balance_due: inv.balance_due,
                    pay_amount: inv.balance_due,
                }));
            } else {
                this.selectedInvoices = [];
            }
            this.recalcInvoiceTotals();
        },

        updateInvoicePayAmount(invoiceId, value) {
            const sel = this.selectedInvoices.find(s => s.invoice_id === invoiceId);
            if (sel) {
                const num = parseFloat(value) || 0;
                sel.pay_amount = Math.min(num, sel.balance_due);
                this.recalcInvoiceTotals();
            }
        },

        recalcInvoiceTotals() {
            this.selectedInvoicesTotal = this.selectedInvoices.reduce((sum, s) => sum + s.balance_due, 0);
            this.selectedInvoicesPayTotal = this.selectedInvoices.reduce((sum, s) => sum + (parseFloat(s.pay_amount) || 0), 0);
        },

        applyInvoicesToEntries() {
            if (this.selectedInvoices.length === 0) return;

            // Get the ledger account id from the first entry (customer account)
            const customerLedgerId = this.receiptEntries[0]?.ledger_account_id;
            if (!customerLedgerId) return;

            // Find the customer account details from ledgerAccounts
            const customerAccount = this.ledgerAccounts.find(a => a.id == customerLedgerId);
            const customerDisplay = customerAccount ? customerAccount.display : '';

            // Build new entries from selected invoices
            const newEntries = this.selectedInvoices.map(sel => ({
                ledger_account_id: customerLedgerId,
                ledger_search: customerDisplay,
                showLedgerDropdown: false,
                particulars: 'Payment for ' + sel.reference,
                credit_amount: parseFloat(sel.pay_amount) || 0,
            }));

            if (newEntries.length > 0) {
                this.receiptEntries = newEntries;
                this.calculateTotal();
            }
        },

        addEntry() {
            this.receiptEntries.push({
                ledger_account_id: '',
                ledger_search: '',
                showLedgerDropdown: false,
                particulars: '',
                credit_amount: 0
            });
        },

        getFilteredLedgerAccounts(entry) {
            const query = (entry.ledger_search || '').toLowerCase();
            if (!query) {
                return this.ledgerAccounts;
            }
            return this.ledgerAccounts.filter(account =>
                account.name.toLowerCase().includes(query) ||
                account.code.toLowerCase().includes(query)
            );
        },

        selectLedgerAccount(entry, account) {
            entry.ledger_account_id = account.id;
            entry.ledger_search = account.display;
            entry.showLedgerDropdown = false;
        },

        removeEntry(index) {
            if (this.receiptEntries.length > 1) {
                this.receiptEntries.splice(index, 1);
                this.calculateTotal();
            }
        },

        calculateTotal() {
            this.totalReceiptAmount = this.receiptEntries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.credit_amount) || 0);
            }, 0);
        },

        formatNumber(value) {
            return parseFloat(value || 0).toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        submitForm(action) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = this.$el.closest('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = action;
            form.appendChild(input);
            form.submit();
        }
    }
}
</script>
@endpush
