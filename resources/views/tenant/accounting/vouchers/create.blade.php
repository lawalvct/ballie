@extends('layouts.tenant')

@section('title', 'Create Voucher - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="voucherForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if(isset($voucher))
                    Duplicate Voucher
                @else
                    Create New Voucher
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                @if(isset($voucher))
                    Creating a copy of voucher {{ $voucher->voucher_number }}
                @else
                    Create a new accounting voucher entry
                @endif
            </p>
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
    <form method="POST" action="{{ route('tenant.accounting.vouchers.store', ['tenant' => $tenant->slug]) }}" class="space-y-6">
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
                                <option value="{{ $type->id }}"
                                        {{ (old('voucher_type_id', $selectedType?->id ?? (isset($voucher) ? $voucher->voucher_type_id : '')) == $type->id) ? 'selected' : '' }}>
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
                               value="{{ old('voucher_date', isset($voucher) ? $voucher->voucher_date->format('Y-m-d') : date('Y-m-d')) }}"
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
        @include('tenant.accounting.vouchers.partials.voucher-entries')

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
        voucherTypeId: '{{ old('voucher_type_id', $selectedType?->id ?? '') }}',
        voucherNumberPreview: 'Auto-generated',
        selectedVoucherTypeName: '{{ $selectedType?->name ?? '' }}',
        voucherTypes: @json($voucherTypes->keyBy('id')),

        init() {
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
            console.log('✅ Voucher form initialized with type:', this.voucherTypeId);
        },

        updateVoucherType() {
            if (this.voucherTypeId && this.voucherTypes[this.voucherTypeId]) {
                const voucherType = this.voucherTypes[this.voucherTypeId];
                this.voucherNumberPreview = voucherType.prefix + 'XXXX';
                this.selectedVoucherTypeName = voucherType.name;
            } else {
                this.voucherNumberPreview = 'Auto-generated';
                this.selectedVoucherTypeName = '';
            }
        }
    }
}
</script>
@endpush
@endsection
