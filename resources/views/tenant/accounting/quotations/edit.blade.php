@extends('layouts.tenant')

@section('title', 'Edit Quotation ' . $quotation->getQuotationNumber() . ' - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Quotation {{ $quotation->getQuotationNumber() }}</h1>
            <p class="mt-2 text-gray-600">Update quotation details</p>
        </div>
        <a href="{{ route('tenant.accounting.quotations.show', [$tenant->slug, $quotation->id]) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <form action="{{ route('tenant.accounting.quotations.update', [$tenant->slug, $quotation->id]) }}" method="POST" id="quotationForm">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                            <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $quotation->expiry_date?->format('Y-m-d')) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="customer_ledger_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                            <select name="customer_ledger_id" id="customer_ledger_id" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->ledgerAccount->id ?? '' }}" {{ old('customer_ledger_id', $quotation->customer_ledger_id) == ($customer->ledgerAccount->id ?? '') ? 'selected' : '' }}>
                                        {{ $customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject', $quotation->subject) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $quotation->reference_number) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                </div>

            @include('tenant.accounting.quotations.partials.quotation-items')

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                        <textarea name="terms_and_conditions" id="terms_and_conditions" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('terms_and_conditions', $quotation->terms_and_conditions) }}</textarea>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes', $quotation->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('tenant.accounting.quotations.show', [$tenant->slug, $quotation->id]) }}"
                       class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center items-center px-6 py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Quotation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection
