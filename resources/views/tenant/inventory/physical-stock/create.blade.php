@extends('layouts.tenant')

@section('title', 'Create Physical Stock Voucher')

@push('styles')
<style>
    /* Page Header Styling */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    /* Modern Card Styling */
    .modern-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .modern-card .card-header {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1.5rem;
    }

    /* Entry Row Styling */
    .entry-row {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e9ecef;
        border-radius: 15px;
        margin-bottom: 20px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
    }

    .entry-row::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 0 0 0 15px;
    }

    .entry-row.has-difference {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
    }

    .entry-row.has-difference::before {
        background: linear-gradient(45deg, #ffc107, #ff6b35);
    }

    .entry-header {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    /* Product Search Styling */
    .product-search {
        position: relative;
    }

    .product-search-input {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .product-search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #667eea;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }

    .search-result-item {
        padding: 15px;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.2s ease;
    }

    .search-result-item:hover {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        transform: translateX(5px);
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    /* Form Input Styling */
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        padding: 0.75rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
    }

    .required::after {
        content: " *";
        color: #dc3545;
    }

    /* Difference Indicator Styling */
    .difference-indicator {
        font-weight: bold;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    .difference-positive {
        background: linear-gradient(45deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 2px solid #b8dacd;
    }

    .difference-negative {
        background: linear-gradient(45deg, #f8d7da, #f5c6cb);
        color: #721c24;
        border: 2px solid #f1b0b7;
    }

    .difference-zero {
        background: linear-gradient(45deg, #e2e3e5, #d6d8db);
        color: #383d41;
        border: 2px solid #ced4da;
    }

    /* Button Styling */
    .btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
    }

    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }

    .btn-outline-danger {
        border: 2px solid #dc3545;
        color: #dc3545;
        background: transparent;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    /* Product Info Alert */
    .product-info .alert {
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    .product-info .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        margin: 0 0.25rem;
    }

    /* Summary Section */
    .summary-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    /* Loading Animations */
    .loading-pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        margin: 2rem 0;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
        opacity: 0.6;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .entry-row {
            padding: 15px;
        }

        .page-header {
            padding: 1.5rem;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Enhanced Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center mb-2">
                    <div class="stats-icon bg-white text-primary me-3">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-0 fw-bold">Create Physical Stock Voucher</h1>
                        <p class="mb-0 opacity-75">Record physical stock count and adjustments with precision</p>
                    </div>
                </div>
                <nav aria-label="breadcrumb" class="mt-3">
                    <ol class="breadcrumb mb-0" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}" class="text-white-50">Physical Stock</a>
                        </li>
                        <li class="breadcrumb-item active text-white">Create Voucher</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
                   class="btn btn-light btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('tenant.inventory.physical-stock.store', ['tenant' => $tenant->slug]) }}" id="voucherForm">
        @csrf

        <!-- Enhanced Voucher Details -->
        <div class="card modern-card shadow mb-4">
            <div class="card-header bg-transparent py-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-dark">Voucher Details</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label required">📅 Voucher Date</label>
                        <input type="date" name="voucher_date" class="form-control @error('voucher_date') is-invalid @enderror"
                               value="{{ old('voucher_date', now()->toDateString()) }}"
                               max="{{ now()->toDateString() }}" required>
                        @error('voucher_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select the date for stock counting</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">🏷️ Reference Number</label>
                        <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                               value="{{ old('reference_number') }}" placeholder="e.g., PSC-2024-001">
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optional external reference</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">📝 Remarks</label>
                        <input type="text" name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                               value="{{ old('remarks') }}" placeholder="e.g., Monthly stock count">
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                        <small class="text-muted">Optional notes about this count</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Product Entries -->
        <div class="card modern-card shadow mb-4">
            <div class="card-header bg-transparent py-4 d-flex justify-content-between align-items-center border-0">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-dark">Product Entries</h6>
                </div>
                <button type="button" class="btn btn-success btn-lg" id="addEntryBtn">
                    <i class="fas fa-plus me-2"></i>Add Product
                </button>
            </div>
            <div class="card-body">
                <div id="entriesContainer">
                    <!-- Entries will be added here dynamically -->
                </div>

                <div class="empty-state" id="noEntriesMessage">
                    <div class="empty-state-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h5 class="text-muted mb-3">No Products Added Yet</h5>
                    <p class="text-muted mb-4">Start building your physical stock voucher by adding products to count.</p>
                    <button type="button" class="btn btn-primary btn-lg" onclick="$('#addEntryBtn').click()">
                        <i class="fas fa-plus me-2"></i>Add Your First Product
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Summary -->
        <div class="card summary-card shadow mb-4" id="summaryCard" style="display: none;">
            <div class="card-header bg-transparent py-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-info text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h6 class="m-0 font-weight-bold text-dark">Summary</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                            <i class="fas fa-cubes text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-primary mb-0 fw-bold" id="totalItems">0</h4>
                            <small class="text-muted fw-semibold">Total Items</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c8 100%);">
                            <i class="fas fa-arrow-up text-success mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-success mb-0 fw-bold" id="totalExcess">₦0.00</h4>
                            <small class="text-muted fw-semibold">Total Excess</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
                            <i class="fas fa-arrow-down text-danger mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-danger mb-0 fw-bold" id="totalShortage">₦0.00</h4>
                            <small class="text-muted fw-semibold">Total Shortage</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);">
                            <i class="fas fa-balance-scale text-info mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-info mb-0 fw-bold" id="netAdjustment">₦0.00</h4>
                            <small class="text-muted fw-semibold">Net Adjustment</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Actions -->
        <div class="card modern-card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
                       class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <div class="btn-group">
                        <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary btn-lg me-3">
                            <i class="fas fa-save me-2"></i>Save as Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Save & Submit for Approval
                        </button>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Draft vouchers can be edited later. Submitted vouchers require approval to process.
                    </small>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Enhanced Entry Template -->
<template id="entryTemplate">
    <div class="entry-row" data-entry-index="">
        <div class="entry-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-box me-2"></i>
                <span>Product Entry #<span class="entry-number"></span></span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-entry">
                <i class="fas fa-trash me-1"></i>Remove
            </button>
        </div>

        <div class="row g-4">
            <!-- Product Selection -->
            <div class="col-md-6">
                <label class="form-label required">🔍 Product</label>
                <div class="product-search">
                    <input type="text" class="form-control product-search-input"
                           placeholder="Type product name or SKU..." autocomplete="off">
                    <input type="hidden" name="entries[][product_id]" class="product-id-input" required>
                    <div class="search-results"></div>
                </div>
                <small class="text-muted">
                    <i class="fas fa-search me-1"></i>Type at least 2 characters to search
                </small>
            </div>

            <!-- Current Stock (Book Quantity) -->
            <div class="col-md-2">
                <label class="form-label">📊 Book Quantity</label>
                <input type="number" class="form-control book-quantity" step="0.0001" readonly>
                <small class="text-muted">System records</small>
            </div>

            <!-- Physical Quantity -->
            <div class="col-md-2">
                <label class="form-label required">📦 Physical Quantity</label>
                <input type="number" name="entries[][physical_quantity]"
                       class="form-control physical-quantity" step="0.0001" min="0" required>
                <small class="text-muted">Counted stock</small>
            </div>

            <!-- Difference -->
            <div class="col-md-2">
                <label class="form-label">⚖️ Difference</label>
                <div class="difference-display d-flex align-items-center justify-content-center" style="min-height: 38px;">
                    <span class="difference-indicator difference-zero">0.00</span>
                </div>
                <small class="text-muted">Physical - Book</small>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <!-- Batch Number -->
            <div class="col-md-3">
                <label class="form-label">🏷️ Batch Number</label>
                <input type="text" name="entries[][batch_number]" class="form-control" placeholder="e.g., LOT001">
                <small class="text-muted">Optional batch info</small>
            </div>

            <!-- Expiry Date -->
            <div class="col-md-3">
                <label class="form-label">📅 Expiry Date</label>
                <input type="date" name="entries[][expiry_date]" class="form-control" min="{{ now()->addDay()->toDateString() }}">
                <small class="text-muted">Future dates only</small>
            </div>

            <!-- Location -->
            <div class="col-md-3">
                <label class="form-label">📍 Location</label>
                <input type="text" name="entries[][location]" class="form-control" placeholder="e.g., Warehouse A">
                <small class="text-muted">Storage location</small>
            </div>

            <!-- Remarks -->
            <div class="col-md-3">
                <label class="form-label">📝 Remarks</label>
                <input type="text" name="entries[][remarks]" class="form-control" placeholder="Optional notes">
                <small class="text-muted">Additional notes</small>
            </div>
        </div>

        <!-- Product Info Display -->
        <div class="product-info mt-4" style="display: none;">
            <div class="alert alert-info border-0 rounded-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    <strong class="product-name"></strong>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-secondary product-sku"></span>
                    <span class="badge bg-info product-category"></span>
                    <span class="badge bg-success product-unit"></span>
                </div>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-tag me-1"></i>Current Rate:
                        <span class="fw-bold">₦<span class="current-rate">0.00</span></span>
                    </small>
                    <small class="text-info">
                        <i class="fas fa-check-circle me-1"></i>Product selected
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let entryIndex = 0;
    let searchTimeout;

    // Add new entry
    $('#addEntryBtn').click(function() {
        addNewEntry();
    });

    // Remove entry
    $(document).on('click', '.remove-entry', function() {
        $(this).closest('.entry-row').remove();
        updateEntryNumbers();
        updateSummary();
        toggleNoEntriesMessage();
    });

    // Product search
    $(document).on('input', '.product-search-input', function() {
        const $input = $(this);
        const $results = $input.siblings('.search-results');
        const query = $input.val().trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $results.hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            searchProducts(query, $results, $input);
        }, 300);
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.product-search').length) {
            $('.search-results').hide();
        }
    });

    // Physical quantity change
    $(document).on('input', '.physical-quantity', function() {
        const $entry = $(this).closest('.entry-row');
        calculateDifference($entry);
        updateSummary();
    });

    // Voucher date change
    $('input[name="voucher_date"]').change(function() {
        const voucherDate = $(this).val();
        // Update all book quantities for the new date
        $('.entry-row').each(function() {
            const $entry = $(this);
            const productId = $entry.find('.product-id-input').val();
            if (productId) {
                updateBookQuantity($entry, productId, voucherDate);
            }
        });
    });

    function addNewEntry() {
        const template = document.getElementById('entryTemplate');
        const clone = template.content.cloneNode(true);
        const $clone = $(clone);

        entryIndex++;
        $clone.find('.entry-row').attr('data-entry-index', entryIndex);
        $clone.find('.entry-number').text(entryIndex);

        $('#entriesContainer').append($clone);
        updateEntryNumbers();
        toggleNoEntriesMessage();
    }

    function updateEntryNumbers() {
        $('.entry-row').each(function(index) {
            $(this).find('.entry-number').text(index + 1);
        });
    }

    function toggleNoEntriesMessage() {
        const hasEntries = $('.entry-row').length > 0;
        $('#noEntriesMessage').toggle(!hasEntries);
        $('#summaryCard').toggle(hasEntries);
    }

    function searchProducts(query, $results, $input) {
        const voucherDate = $('input[name="voucher_date"]').val();

        $.ajax({
            url: '{{ route("tenant.inventory.physical-stock.products-search", ["tenant" => $tenant->slug]) }}',
            method: 'GET',
            data: {
                search: query,
                as_of_date: voucherDate
            },
            success: function(products) {
                $results.empty();

                if (products.length === 0) {
                    $results.html('<div class="search-result-item text-muted">No products found</div>');
                } else {
                    products.forEach(function(product) {
                        const item = $(`
                            <div class="search-result-item" data-product-id="${product.id}">
                                <strong>${product.name}</strong>
                                <span class="badge bg-secondary ms-2">${product.sku}</span>
                                <br>
                                <small class="text-muted">
                                    ${product.category} | ${product.unit} |
                                    Stock: ${product.current_stock} |
                                    Rate: ₦${product.average_rate}
                                </small>
                            </div>
                        `);

                        item.click(function() {
                            selectProduct($input, product);
                        });

                        $results.append(item);
                    });
                }

                $results.show();
            },
            error: function() {
                $results.html('<div class="search-result-item text-danger">Error loading products</div>');
                $results.show();
            }
        });
    }

    function selectProduct($input, product) {
        const $entry = $input.closest('.entry-row');
        const voucherDate = $('input[name="voucher_date"]').val();

        // Set product details
        $input.val(product.name);
        $entry.find('.product-id-input').val(product.id);

        // Update product info display
        $entry.find('.product-name').text(product.name);
        $entry.find('.product-sku').text(product.sku);
        $entry.find('.product-category').text(product.category);
        $entry.find('.product-unit').text(product.unit);
        $entry.find('.current-rate').text(parseFloat(product.average_rate).toFixed(2));
        $entry.find('.product-info').show();

        // Set book quantity
        $entry.find('.book-quantity').val(parseFloat(product.current_stock).toFixed(4));

        // Hide search results
        $input.siblings('.search-results').hide();

        // Calculate difference if physical quantity is entered
        calculateDifference($entry);
        updateSummary();
    }

    function updateBookQuantity($entry, productId, voucherDate) {
        $.ajax({
            url: '{{ route("tenant.inventory.physical-stock.product-stock", ["tenant" => $tenant->slug]) }}',
            method: 'GET',
            data: {
                product_id: productId,
                as_of_date: voucherDate
            },
            success: function(data) {
                $entry.find('.book-quantity').val(parseFloat(data.stock_quantity).toFixed(4));
                $entry.find('.current-rate').text(parseFloat(data.average_rate).toFixed(2));
                calculateDifference($entry);
                updateSummary();
            }
        });
    }

    function calculateDifference($entry) {
        const bookQty = parseFloat($entry.find('.book-quantity').val()) || 0;
        const physicalQty = parseFloat($entry.find('.physical-quantity').val()) || 0;
        const difference = physicalQty - bookQty;

        const $indicator = $entry.find('.difference-indicator');
        $indicator.text(Math.abs(difference).toFixed(4));

        // Update styling based on difference
        $indicator.removeClass('difference-positive difference-negative difference-zero');
        $entry.removeClass('has-difference');

        if (difference > 0) {
            $indicator.addClass('difference-positive');
            $indicator.text('+' + difference.toFixed(4));
            $entry.addClass('has-difference');
        } else if (difference < 0) {
            $indicator.addClass('difference-negative');
            $indicator.text(difference.toFixed(4));
            $entry.addClass('has-difference');
        } else {
            $indicator.addClass('difference-zero');
        }
    }

    function updateSummary() {
        let totalItems = 0;
        let totalExcess = 0;
        let totalShortage = 0;

        $('.entry-row').each(function() {
            const $entry = $(this);
            const bookQty = parseFloat($entry.find('.book-quantity').val()) || 0;
            const physicalQty = parseFloat($entry.find('.physical-quantity').val()) || 0;
            const currentRate = parseFloat($entry.find('.current-rate').text()) || 0;
            const difference = physicalQty - bookQty;
            const differenceValue = Math.abs(difference) * currentRate;

            if ($entry.find('.product-id-input').val()) {
                totalItems++;

                if (difference > 0) {
                    totalExcess += differenceValue;
                } else if (difference < 0) {
                    totalShortage += differenceValue;
                }
            }
        });

        const netAdjustment = totalExcess - totalShortage;

        $('#totalItems').text(totalItems);
        $('#totalExcess').text('₦' + totalExcess.toFixed(2));
        $('#totalShortage').text('₦' + totalShortage.toFixed(2));
        $('#netAdjustment').text('₦' + netAdjustment.toFixed(2));

        // Update net adjustment color
        const $netElement = $('#netAdjustment');
        $netElement.removeClass('text-success text-danger text-info');
        if (netAdjustment > 0) {
            $netElement.addClass('text-success');
        } else if (netAdjustment < 0) {
            $netElement.addClass('text-danger');
        } else {
            $netElement.addClass('text-info');
        }
    }

    // Form validation
    $('#voucherForm').submit(function(e) {
        const hasEntries = $('.entry-row').length > 0;

        if (!hasEntries) {
            e.preventDefault();
            alert('Please add at least one product entry.');
            return false;
        }

        // Validate all entries have products selected
        let allValid = true;
        $('.entry-row').each(function() {
            const productId = $(this).find('.product-id-input').val();
            if (!productId) {
                allValid = false;
                $(this).find('.product-search-input').addClass('is-invalid');
            } else {
                $(this).find('.product-search-input').removeClass('is-invalid');
            }
        });

        if (!allValid) {
            e.preventDefault();
            alert('Please select products for all entries.');
            return false;
        }
    });

    // Add first entry by default
    addNewEntry();
});
</script>
@endpush
