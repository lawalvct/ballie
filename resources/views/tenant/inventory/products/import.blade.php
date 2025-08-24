@extends('layouts.tenant')

@section('title', 'Import Products')
@section('page-title', 'Import Products')
@section('page-description')
    <span class="hidden md:inline">Upload a CSV file to import multiple products at once.</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-8">
    <h2 class="text-2xl font-bold mb-4 text-gray-900">Bulk Import Products</h2>
    <p class="mb-4 text-gray-700 text-sm">
        Upload a CSV file to import multiple products at once.<br>
        <span class="font-semibold">Required columns:</span>
        <span class="text-xs">name, type, sku, category_id, brand, description, purchase_rate, sales_rate, mrp, primary_unit_id, unit_conversion_factor, barcode, hsn_code, tax_rate, opening_stock, reorder_level, maintain_stock, stock_asset_account_id, sales_account_id, purchase_account_id, is_active, is_saleable, is_purchasable</span>
    </p>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
    @if(session('import_errors'))
        <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-800">
            <strong>Import Errors:</strong>
            <ul class="list-disc pl-5">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.inventory.products.import.process', ['tenant' => $tenant->slug]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
            <input type="file" name="import_file" accept=".csv" required class="block w-full border border-gray-300 rounded-lg px-3 py-2" />
        </div>
        <div class="flex items-center justify-between mt-6">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import Products
            </button>
            <a href="{{ asset('sample-product-import.csv') }}" class="text-purple-700 hover:underline text-sm">Download Sample CSV</a>
        </div>
    </form>
    <div class="mt-4 text-xs text-gray-500">
        <strong>Note:</strong> Only CSV files are supported. For best results, use the sample template.<br>
        <span class="font-semibold">Tip:</span> Download your categories and units first to get their IDs for mapping.
    </div>
</div>
@endsection