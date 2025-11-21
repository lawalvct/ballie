@extends('layouts.tenant')

@section('title', 'Create Voucher - ' . $tenant->name)
@section('page-title',  " Create New Voucher")
@section('page-description', ' Create a new accounting voucher entry')
@section('content')
<div class="space-y-6" x-data="voucherForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3 flex-wrap">
            <!-- Common Voucher Type Buttons -->
            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => 'jv']) }}"
               class="inline-flex items-center px-4 py-2 border border-blue-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Journal
            </a>

            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => 'pv']) }}"
               class="inline-flex items-center px-4 py-2 border border-red-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Payment
            </a>

            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => 'rv']) }}"
               class="inline-flex items-center px-4 py-2 border border-green-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Receipt
            </a>

            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => 'cv']) }}"
               class="inline-flex items-center px-4 py-2 border border-purple-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                Contra
            </a>

            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => 'cn']) }}"
               class="inline-flex items-center px-4 py-2 border border-orange-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                </svg>
                Credit Note
            </a>

            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => 'dn']) }}"
               class="inline-flex items-center px-4 py-2 border border-indigo-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                </svg>
                Debit Note
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Vouchers
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.accounting.vouchers.store', ['tenant' => $tenant->slug]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Voucher Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Voucher Information</h3>
                    <span x-show="selectedVoucherTypeName"
                          x-text="selectedVoucherTypeName"
                          class="font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-full text-sm">
                    </span>
                </div>
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
                                @php
                                    $defaultTypeId = $selectedType?->id ?? (isset($voucher) ? $voucher->voucher_type_id : $voucherTypes->firstWhere('code', 'JV')?->id);
                                @endphp
                                <option value="{{ $type->id }}"
                                        {{ (old('voucher_type_id', $defaultTypeId) == $type->id) ? 'selected' : '' }}>
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
                            Voucher Date <span class="text-red-500">*</span> <span x-text="dayName" class="ml-2 text-sm text-gray-600 w-24"></span>
                        </label>
                        <input type="date"
                               name="voucher_date"
                               id="voucher_date"
                               value="{{ old('voucher_date', isset($voucher) ? $voucher->voucher_date->format('Y-m-d') : date('Y-m-d')) }}"
                               x-model="voucherDate"
                               @change="updateDayName()"
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
                               value="{{ old('reference_number', isset($voucher) ? $voucher->reference_number : '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('reference_number') border-red-300 @enderror"
                               placeholder="Optional reference">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Voucher Number Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Number
                        </label>
                        <div class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                            <span x-text="voucherNumberPreview"></span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto-generated on save</p>
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
                              placeholder="Enter voucher description or narration">{{ old('narration', isset($voucher) ? $voucher->narration : '') }}</textarea>
                    @error('narration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Voucher Entries -->
        @php
            $voucherEntryType = 'journal'; // default

            // 1. Check URL type parameter first (highest priority)
            $urlType = strtolower(request()->get('type', ''));
            if ($urlType === 'pv') {
                $voucherEntryType = 'payment';
            } elseif ($urlType === 'rv') {
                $voucherEntryType = 'receipt';
            } elseif ($urlType === 'cn') {
                $voucherEntryType = 'credit-note';
            } elseif ($urlType === 'dn') {
                $voucherEntryType = 'debit-note';
            } elseif ($urlType === 'cv') {
                $voucherEntryType = 'contra';
            }
            // 2. Check selected voucher type from URL parameter or old input
            else {
                $selectedVoucherTypeId = request()->get('voucher_type_id') ?: old('voucher_type_id');
                if ($selectedVoucherTypeId) {
                    $selectedVoucherType = $voucherTypes->firstWhere('id', $selectedVoucherTypeId);
                    if ($selectedVoucherType) {
                        $code = strtolower($selectedVoucherType->code ?? '');
                        if ($code === 'pv' || str_contains($code, 'payment')) {
                            $voucherEntryType = 'payment';
                        } elseif ($code === 'rv' || str_contains($code, 'receipt')) {
                            $voucherEntryType = 'receipt';
                        } elseif ($code === 'cn' || str_contains($code, 'credit') && str_contains($code, 'note')) {
                            $voucherEntryType = 'credit-note';
                        } elseif ($code === 'dn' || str_contains($code, 'debit') && str_contains($code, 'note')) {
                            $voucherEntryType = 'debit-note';
                        } elseif ($code === 'cv' || str_contains($code, 'contra')) {
                            $voucherEntryType = 'contra';
                        }
                    }
                }
            }

            // Define partial paths with fallback
            $partialPaths = [
                'payment' => 'tenant.accounting.vouchers.partials.payment-entries',
                'receipt' => 'tenant.accounting.vouchers.partials.receipt-entries',
                'credit-note' => 'tenant.accounting.vouchers.partials.credit-note-entries',
                'debit-note' => 'tenant.accounting.vouchers.partials.debit-note-entries',
                'contra' => 'tenant.accounting.vouchers.partials.contra-entries',
                'journal' => 'tenant.accounting.vouchers.partials.voucher-entries'
            ];

            $selectedPartial = $partialPaths[$voucherEntryType] ?? $partialPaths['journal'];
            $partialExists = view()->exists($selectedPartial);
        @endphp

        @if($partialExists)
            @include($selectedPartial)
        @else
            {{-- Fallback for unavailable partial --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Voucher Entries</h3>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Voucher Entry Interface Unavailable
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>The entry interface for "{{ ucfirst($voucherEntryType) }}" voucher type is not available.</p>
                                    <p class="mt-1">
                                        <strong>Attempted to load:</strong> <code>{{ $selectedPartial }}</code>
                                    </p>
                                    <p class="mt-2">
                                        Please select a different voucher type or
                                        <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}"
                                           class="font-medium underline">use the default journal entry</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </form>

    <!-- Quick Add Ledger Account Modal (Outside Form) -->
    <div id="addLedgerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add New Ledger Account</h3>
                    <button type="button" onclick="closeAddLedgerModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="addLedgerForm" onsubmit="addNewLedgerAccount(event)">
                    <div class="space-y-4">
                        <!-- Account Name -->
                        <div>
                            <label for="ledger_name" class="block text-sm font-medium text-gray-700">
                                Account Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="ledger_name"
                                   name="name"
                                   required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <!-- Account Code -->
                        <div>
                            <label for="ledger_code" class="block text-sm font-medium text-gray-700">
                                Account Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="ledger_code"
                                   name="code"
                                   required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <!-- Account Group -->
                        <div>
                            <label for="ledger_account_group_id" class="block text-sm font-medium text-gray-700">
                                Account Group <span class="text-red-500">*</span>
                            </label>
                            <select id="ledger_account_group_id"
                                    name="account_group_id"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Account Group</option>
                                @foreach($ledgerAccounts->pluck('accountGroup')->filter()->unique('id')->sortBy('name') as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Account Type -->
                        <div>
                            <label for="ledger_account_type" class="block text-sm font-medium text-gray-700">
                                Account Type <span class="text-red-500">*</span>
                            </label>
                            <select id="ledger_account_type"
                                    name="account_type"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Account Type</option>
                                <option value="asset">Asset</option>
                                <option value="liability">Liability</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                                <option value="equity">Equity</option>
                            </select>
                        </div>

                        <!-- Opening Balance -->
                        <div>
                            <label for="ledger_opening_balance" class="block text-sm font-medium text-gray-700">
                                Opening Balance
                            </label>
                            <input type="number"
                                   id="ledger_opening_balance"
                                   name="opening_balance"
                                   step="0.01"
                                   min="0"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="ledger_description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea id="ledger_description"
                                      name="description"
                                      rows="2"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button"
                                onclick="closeAddLedgerModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <span id="addLedgerSubmitText">Add Account</span>
                            <svg id="addLedgerSpinner" class="hidden animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function voucherForm() {
    return {
        voucherTypeId: '{{ old('voucher_type_id', $selectedType?->id ?? (isset($voucher) ? $voucher->voucher_type_id : $voucherTypes->firstWhere('code', 'JV')?->id ?? '')) }}',
        voucherNumberPreview: 'Auto-generated',
        selectedVoucherTypeName: '{{ $selectedType?->name ?? (isset($voucher) ? '' : $voucherTypes->firstWhere('code', 'JV')?->name ?? '') }}',
        voucherTypes: @json($voucherTypes->keyBy('id')),
        voucherDate: '{{ old('voucher_date', isset($voucher) ? $voucher->voucher_date->format('Y-m-d') : date('Y-m-d')) }}',
        dayName: '',

        init() {
            // Check if voucher_type_id is in URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const urlVoucherTypeId = urlParams.get('voucher_type_id');
            if (urlVoucherTypeId) {
                this.voucherTypeId = urlVoucherTypeId;
            }

            // Initialize with old input or selected type from URL parameter
            if (this.voucherTypeId) {
                // Trigger the select element to update visually
                this.$nextTick(() => {
                    const selectElement = document.getElementById('voucher_type_id');
                    if (selectElement) {
                        selectElement.value = this.voucherTypeId;
                        // Trigger change event to update preview
                        selectElement.dispatchEvent(new Event('change'));
                    }
                });
            }
            this.updateVoucherType();
            this.updateDayName();
            console.log('✅ Voucher form initialized with type:', this.voucherTypeId);
        },

        updateVoucherType() {
            if (this.voucherTypeId && this.voucherTypes[this.voucherTypeId]) {
                const voucherType = this.voucherTypes[this.voucherTypeId];
                this.voucherNumberPreview = voucherType.prefix + 'XXXX';
                this.selectedVoucherTypeName = voucherType.name;

                // Auto-redirect to the correct URL with type parameter
                const code = (voucherType.code || '').toLowerCase();
                const currentUrl = new URL(window.location);
                const currentType = currentUrl.searchParams.get('type');

                // Determine target type parameter based on voucher code
                let targetType = null;
                if (code === 'pv' || code.includes('payment')) {
                    targetType = 'pv';
                } else if (code === 'rv' || code.includes('receipt')) {
                    targetType = 'rv';
                } else if (code === 'cn' || (code.includes('credit') && code.includes('note'))) {
                    targetType = 'cn';
                } else if (code === 'dn' || (code.includes('debit') && code.includes('note'))) {
                    targetType = 'dn';
                } else if (code === 'cv' || code.includes('contra')) {
                    targetType = 'cv';
                }

                // Only redirect if the type parameter needs to change
                if (currentType !== targetType) {
                    if (targetType) {
                        currentUrl.searchParams.set('type', targetType);
                    } else {
                        currentUrl.searchParams.delete('type');
                    }

                    // Preserve form data by adding voucher type to URL
                    currentUrl.searchParams.set('voucher_type_id', this.voucherTypeId);

                    // Redirect to new URL
                    window.location.href = currentUrl.toString();
                }
            } else {
                this.voucherNumberPreview = 'Auto-generated';
                this.selectedVoucherTypeName = '';
            }
        },
        updateDayName() {
            if (this.voucherDate) {
                const date = new Date(this.voucherDate);
                const timezoneOffset = date.getTimezoneOffset() * 60000;
                const adjustedDate = new Date(date.getTime() + timezoneOffset);
                this.dayName = adjustedDate.toLocaleDateString('en-US', { weekday: 'long' });
            } else {
                this.dayName = '';
            }
        }
    }
}
</script>
@endpush
@endsection
