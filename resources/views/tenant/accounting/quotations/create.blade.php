@extends('layouts.tenant')

@section('title', 'Create Quotation - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Quotation</h1>
            <p class="mt-2 text-gray-600">Create a new quotation for your customer</p>
        </div>
        <a href="{{ route('tenant.accounting.quotations.index', $tenant->slug) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <form action="{{ route('tenant.accounting.quotations.store', $tenant->slug) }}" method="POST" id="quotationForm">
        @csrf
        
        <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                            <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', now()->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            @error('quotation_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $defaultExpiryDate) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            @error('expiry_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="customer_ledger_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                            <select name="customer_ledger_id" id="customer_ledger_id" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->ledgerAccount->id ?? '' }}" {{ old('customer_ledger_id') == ($customer->ledgerAccount->id ?? '') ? 'selected' : '' }}>
                                        {{ $customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_ledger_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            @error('reference_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            <!-- Items -->
            @include('tenant.accounting.quotations.partials.quotation-items')

            <!-- Terms & Notes -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                        <textarea name="terms_and_conditions" id="terms_and_conditions" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('terms_and_conditions') }}</textarea>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <button type="submit" name="action" value="save"
                            class="inline-flex justify-center items-center px-6 py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="save_and_send"
                            class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Save & Mark as Sent
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection
