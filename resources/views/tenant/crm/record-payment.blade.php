@extends('layouts.tenant')

@section('title', 'Record Payment - ' . $tenant->name)
@section('page-title', 'Record Payment')
@section('page-description', 'Record customer or vendor payment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-lg rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
            <h3 class="text-xl font-bold text-gray-900">Record Payment</h3>
            <p class="text-sm text-gray-600 mt-1">Record payment received from customer or vendor</p>
        </div>

        <form method="POST" action="{{ route('tenant.crm.store-payment', $tenant->slug) }}" class="p-6 space-y-6" x-data="paymentForm()">
            @csrf

            <!-- Party Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment From <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all" :class="partyType === 'customer' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'">
                        <input type="radio" name="party_type" value="customer" x-model="partyType" class="sr-only" required>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3" :class="partyType === 'customer' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-semibold" :class="partyType === 'customer' ? 'text-green-700' : 'text-gray-700'">Customer</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all" :class="partyType === 'vendor' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-gray-400'">
                        <input type="radio" name="party_type" value="vendor" x-model="partyType" class="sr-only" required>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3" :class="partyType === 'vendor' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="font-semibold" :class="partyType === 'vendor' ? 'text-green-700' : 'text-gray-700'">Vendor</span>
                        </div>
                    </label>
                </div>
                @error('party_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer/Vendor Selection -->
            <div>
                <div x-show="partyType === 'customer'">
                    <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-2">Select Customer <span class="text-red-500">*</span></label>
                    <input type="text" id="customer_search" x-model="customerSearch" @input="filterCustomers" placeholder="Search customer..." class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 mb-2">
                    <select id="customer_id" x-model="selectedParty" size="5" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <template x-for="customer in filteredCustomers" :key="customer.id">
                            <option :value="customer.id" x-text="customer.name"></option>
                        </template>
                    </select>
                </div>

                <div x-show="partyType === 'vendor'">
                    <label for="vendor_search" class="block text-sm font-medium text-gray-700 mb-2">Select Vendor <span class="text-red-500">*</span></label>
                    <input type="text" id="vendor_search" x-model="vendorSearch" @input="filterVendors" placeholder="Search vendor..." class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 mb-2">
                    <select id="vendor_id" x-model="selectedParty" size="5" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <template x-for="vendor in filteredVendors" :key="vendor.id">
                            <option :value="vendor.id" x-text="vendor.name"></option>
                        </template>
                    </select>
                </div>
                
                <input type="hidden" name="party_ledger_id" :value="selectedParty" required>
                @error('party_ledger_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Receipt Date -->
            <div>
                <label for="receipt_date" class="block text-sm font-medium text-gray-700 mb-2">Receipt Date <span class="text-red-500">*</span></label>
                <input type="date" name="receipt_date" id="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('receipt_date') border-red-300 @enderror" required>
                @error('receipt_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-semibold">₦</span>
                    <input type="number" step="0.01" name="amount" id="amount" x-model="amount" @input="formatAmount" value="{{ old('amount') }}" placeholder="0.00" class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('amount') border-red-300 @enderror" required>
                </div>
                <p x-show="amount > 0" class="mt-1 text-sm text-green-600 font-semibold">₦<span x-text="formattedAmount"></span></p>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank/Cash Account -->
            <div>
                <label for="bank_account_id" class="block text-sm font-medium text-gray-700 mb-2">Bank/Cash Account <span class="text-red-500">*</span></label>
                <select name="bank_account_id" id="bank_account_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('bank_account_id') border-red-300 @enderror" required>
                    <option value="">-- Select Bank/Cash Account --</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} ({{ $account->code }})
                        </option>
                    @endforeach
                </select>
                @error('bank_account_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reference Number -->
            <div>
                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="Optional reference" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Optional payment notes" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('tenant.crm.index', $tenant->slug) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 font-medium shadow-lg hover:shadow-xl transition-all">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function paymentForm() {
    return {
        partyType: '{{ old('party_type', 'customer') }}',
        selectedParty: '{{ old('party_ledger_id') }}',
        amount: '{{ old('amount') }}',
        formattedAmount: '',
        customerSearch: '',
        vendorSearch: '',
        customers: @json($customers->filter(fn($c) => $c->ledgerAccount)->map(fn($c) => ['id' => $c->ledgerAccount->id, 'name' => $c->full_name])->values()),
        vendors: @json($vendors->filter(fn($v) => $v->ledgerAccount)->map(fn($v) => ['id' => $v->ledgerAccount->id, 'name' => $v->full_name])->values()),
        filteredCustomers: [],
        filteredVendors: [],
        
        init() {
            this.filteredCustomers = this.customers;
            this.filteredVendors = this.vendors;
        },
        
        filterCustomers() {
            const search = this.customerSearch.toLowerCase();
            this.filteredCustomers = this.customers.filter(c => 
                c.name.toLowerCase().includes(search)
            );
        },
        
        filterVendors() {
            const search = this.vendorSearch.toLowerCase();
            this.filteredVendors = this.vendors.filter(v => 
                v.name.toLowerCase().includes(search)
            );
        },
        
        formatAmount() {
            const value = parseFloat(this.amount) || 0;
            this.formattedAmount = value.toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}
</script>
@endpush
@endsection
