@extends('layouts.tenant')

@section('title', 'Edit Physical Stock Voucher')

@push('styles')
<style>
    /* Enhanced Page Header */
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

    /* Modern Card Styling */
    .modern-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
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
        border-bottom: 2px solid #e9ecef;
    }

    /* Enhanced Form Controls */
    .enhanced-form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.9);
    }

    .enhanced-form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }

    .enhanced-form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .enhanced-form-label.required::after {
        content: " *";
        color: #dc3545;
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
        border-radius: 15px 0 0 15px;
    }

    .entry-row.has-difference {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
    }

    .entry-row.has-difference::before {
        background: linear-gradient(45deg, #ffc107, #ff9500);
    }

    .entry-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Product Search */
    .product-search {
        position: relative;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        border-top: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .search-result-item {
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.2s ease;
    }

    .search-result-item:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    /* Difference Indicators */
    .difference-indicator {
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        animation: bounceIn 0.6s ease-out;
    }

    .difference-positive {
        background: linear-gradient(135deg, #d4edda, #28a745);
        color: #155724;
        border: 2px solid rgba(40, 167, 69, 0.3);
    }

    .difference-negative {
        background: linear-gradient(135deg, #f8d7da, #dc3545);
        color: #721c24;
        border: 2px solid rgba(220, 53, 69, 0.3);
    }

    .difference-zero {
        background: linear-gradient(135deg, #e2e3e5, #6c757d);
        color: #495057;
        border: 2px solid rgba(108, 117, 125, 0.3);
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Action Buttons */
    .action-btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 2px solid transparent;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-group .action-btn {
        margin-right: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .entry-row {
            padding: 15px;
        }

        .modern-card .card-header {
            padding: 1rem;
        }
    }
</style>
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Enhanced Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0" style="background: transparent;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
                               class="text-white-50 text-decoration-none">
                                <i class="fas fa-clipboard-list me-1"></i>Physical Stock
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('tenant.inventory.physical-stock.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                               class="text-white-50 text-decoration-none">
                                {{ $voucher->voucher_number }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-white">
                            <i class="fas fa-edit me-1"></i>Edit
                        </li>
                    </ol>
                </nav>
                <h1 class="mb-2 fw-bold">
                    <i class="fas fa-edit me-3"></i>
                    Edit Physical Stock Voucher
                </h1>
                <p class="mb-0 text-white-50 fs-5">
                    <i class="fas fa-hashtag me-2"></i>{{ $voucher->voucher_number }}
                    <span class="ms-3">
                        <i class="fas fa-calendar me-2"></i>{{ $voucher->voucher_date->format('d M Y') }}
                    </span>
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('tenant.inventory.physical-stock.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                   class="action-btn btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Voucher
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('tenant.inventory.physical-stock.update', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" id="voucherForm">
        @csrf
        @method('PUT')

        <!-- Enhanced Voucher Details -->
        <div class="modern-card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Voucher Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="enhanced-form-label required">📅 Voucher Date</label>
                        <input type="date" name="voucher_date" class="form-control enhanced-form-control @error('voucher_date') is-invalid @enderror"
                               value="{{ old('voucher_date', $voucher->voucher_date->toDateString()) }}"
                               max="{{ now()->toDateString() }}" required>
                        @error('voucher_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="enhanced-form-label">🔗 Reference Number</label>
                        <input type="text" name="reference_number" class="form-control enhanced-form-control @error('reference_number') is-invalid @enderror"
                               value="{{ old('reference_number', $voucher->reference_number) }}" placeholder="Optional reference number">
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="enhanced-form-label">💬 Remarks</label>
                        <input type="text" name="remarks" class="form-control enhanced-form-control @error('remarks') is-invalid @enderror"
                               value="{{ old('remarks', $voucher->remarks) }}" placeholder="Optional remarks or notes">
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Product Entries -->
        <div class="modern-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-boxes text-primary me-2"></i>
                    Product Entries
                </h6>
                <button type="button" class="action-btn btn btn-success btn-sm" id="addEntryBtn">
                    <i class="fas fa-plus me-1"></i>Add Product
                </button>
            </div>
            <div class="card-body">
                <div id="entriesContainer">
                    @foreach($voucher->entries as $index => $entry)
                        <div class="entry-row {{ $entry->hasDifference() ? 'has-difference' : '' }}" data-entry-index="{{ $index + 1 }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0">Product Entry #<span class="entry-number">{{ $index + 1 }}</span></h6>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-entry">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="row">
                                <!-- Product Selection -->
                                <div class="col-md-6">
                                    <label class="form-label required">Product</label>
                                    <div class="product-search">
                                        <input type="text" class="form-control product-search-input"
                                               value="{{ $entry->product->name }}" placeholder="Search products..." autocomplete="off">
                                        <input type="hidden" name="entries[{{ $index }}][product_id]" class="product-id-input" value="{{ $entry->product_id }}" required>
                                        <div class="search-results"></div>
                                    </div>
                                    <small class="text-muted">Type to search for products</small>
                                </div>

                                <!-- Current Stock (Book Quantity) -->
                                <div class="col-md-2">
                                    <label class="form-label">Book Quantity</label>
                                    <input type="number" class="form-control book-quantity" value="{{ $entry->book_quantity }}" step="0.0001" readonly>
                                    <small class="text-muted">As per system</small>
                                </div>

                                <!-- Physical Quantity -->
                                <div class="col-md-2">
                                    <label class="form-label required">Physical Quantity</label>
                                    <input type="number" name="entries[{{ $index }}][physical_quantity]"
                                           class="form-control physical-quantity" value="{{ $entry->physical_quantity }}" step="0.0001" min="0" required>
                                    <small class="text-muted">Actual count</small>
                                </div>

                                <!-- Difference -->
                                <div class="col-md-2">
                                    <label class="form-label">Difference</label>
                                    <div class="difference-display">
                                        <span class="difference-indicator {{ $entry->isExcess() ? 'difference-positive' : ($entry->isShortage() ? 'difference-negative' : 'difference-zero') }}">
                                            {{ $entry->difference_quantity > 0 ? '+' : '' }}{{ number_format($entry->difference_quantity, 4) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">Physical - Book</small>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <!-- Batch Number -->
                                <div class="col-md-3">
                                    <label class="form-label">Batch Number</label>
                                    <input type="text" name="entries[{{ $index }}][batch_number]" class="form-control" value="{{ $entry->batch_number }}">
                                </div>

                                <!-- Expiry Date -->
                                <div class="col-md-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="date" name="entries[{{ $index }}][expiry_date]" class="form-control"
                                           value="{{ $entry->expiry_date ? $entry->expiry_date->toDateString() : '' }}" min="{{ now()->addDay()->toDateString() }}">
                                </div>

                                <!-- Location -->
                                <div class="col-md-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="entries[{{ $index }}][location]" class="form-control"
                                           value="{{ $entry->location }}" placeholder="Warehouse/Shelf">
                                </div>

                                <!-- Remarks -->
                                <div class="col-md-3">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="entries[{{ $index }}][remarks]" class="form-control"
                                           value="{{ $entry->remarks }}" placeholder="Optional notes">
                                </div>
                            </div>

                            <!-- Product Info Display -->
                            <div class="product-info mt-3">
                                <div class="alert alert-info mb-0">
                                    <strong class="product-name">{{ $entry->product->name }}</strong>
                                    <span class="badge bg-secondary product-sku">{{ $entry->product->sku }}</span>
                                    @if($entry->product->category)
                                        <span class="badge bg-info product-category">{{ $entry->product->category->name }}</span>
                                    @endif
                                    @if($entry->product->primaryUnit)
                                        <span class="badge bg-success product-unit">{{ $entry->product->primaryUnit->name }}</span>
                                    @endif
                                    <div class="mt-2">
                                        <small>Current Rate: ₦<span class="current-rate">{{ number_format($entry->current_rate, 2) }}</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-3" id="noEntriesMessage" style="{{ $voucher->entries->count() > 0 ? 'display: none;' : '' }}">
                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No products added yet. Click "Add Product" to get started.</p>
                </div>
            </div>
        </div>

        <!-- Enhanced Actions -->
        <div class="modern-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tenant.inventory.physical-stock.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                       class="action-btn btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel Changes
                    </a>
                    <div class="text-center mx-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Make sure all physical quantities are accurate before updating
                        </small>
                    </div>
                    <button type="submit" class="action-btn btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Voucher
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Entry Template (same as create page) -->
<template id="entryTemplate">
    <div class="entry-row" data-entry-index="">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h6 class="mb-0">Product Entry #<span class="entry-number"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-entry">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="row">
            <!-- Product Selection -->
            <div class="col-md-6">
                <label class="form-label required">Product</label>
                <div class="product-search">
                    <input type="text" class="form-control product-search-input"
                           placeholder="Search products..." autocomplete="off">
                    <input type="hidden" name="entries[][product_id]" class="product-id-input" required>
                    <div class="search-results"></div>
                </div>
                <small class="text-muted">Type to search for products</small>
            </div>

            <!-- Current Stock (Book Quantity) -->
            <div class="col-md-2">
                <label class="form-label">Book Quantity</label>
                <input type="number" class="form-control book-quantity" step="0.0001" readonly>
                <small class="text-muted">As per system</small>
            </div>

            <!-- Physical Quantity -->
            <div class="col-md-2">
                <label class="form-label required">Physical Quantity</label>
                <input type="number" name="entries[][physical_quantity]"
                       class="form-control physical-quantity" step="0.0001" min="0" required>
                <small class="text-muted">Actual count</small>
            </div>

            <!-- Difference -->
            <div class="col-md-2">
                <label class="form-label">Difference</label>
                <div class="difference-display">
                    <span class="difference-indicator difference-zero">0.00</span>
                </div>
                <small class="text-muted">Physical - Book</small>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Batch Number -->
            <div class="col-md-3">
                <label class="form-label">Batch Number</label>
                <input type="text" name="entries[][batch_number]" class="form-control">
            </div>

            <!-- Expiry Date -->
            <div class="col-md-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="entries[][expiry_date]" class="form-control" min="{{ now()->addDay()->toDateString() }}">
            </div>

            <!-- Location -->
            <div class="col-md-3">
                <label class="form-label">Location</label>
                <input type="text" name="entries[][location]" class="form-control" placeholder="Warehouse/Shelf">
            </div>

            <!-- Remarks -->
            <div class="col-md-3">
                <label class="form-label">Remarks</label>
                <input type="text" name="entries[][remarks]" class="form-control" placeholder="Optional notes">
            </div>
        </div>

        <!-- Product Info Display -->
        <div class="product-info mt-3" style="display: none;">
            <div class="alert alert-info mb-0">
                <strong class="product-name"></strong>
                <span class="badge bg-secondary product-sku"></span>
                <span class="badge bg-info product-category"></span>
                <span class="badge bg-success product-unit"></span>
                <div class="mt-2">
                    <small>Current Rate: ₦<span class="current-rate">0.00</span></small>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let entryIndex = {{ $voucher->entries->count() }};
    let searchTimeout;

    // Add new entry
    $('#addEntryBtn').click(function() {
        addNewEntry();
    });

    // Remove entry
    $(document).on('click', '.remove-entry', function() {
        $(this).closest('.entry-row').remove();
        updateEntryNumbers();
        toggleNoEntriesMessage();
    });

    // Product search (same as create page)
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

        // Update the name attributes to use the correct index
        $clone.find('input[name="entries[][product_id]"]').attr('name', `entries[${entryIndex}][product_id]`);
        $clone.find('input[name="entries[][physical_quantity]"]').attr('name', `entries[${entryIndex}][physical_quantity]`);
        $clone.find('input[name="entries[][batch_number]"]').attr('name', `entries[${entryIndex}][batch_number]`);
        $clone.find('input[name="entries[][expiry_date]"]').attr('name', `entries[${entryIndex}][expiry_date]`);
        $clone.find('input[name="entries[][location]"]').attr('name', `entries[${entryIndex}][location]`);
        $clone.find('input[name="entries[][remarks]"]').attr('name', `entries[${entryIndex}][remarks]`);

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

    // Initialize calculations for existing entries
    $('.entry-row').each(function() {
        calculateDifference($(this));
    });
});
</script>
@endpush
