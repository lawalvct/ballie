@extends('layouts.tenant')

@section('title', 'Physical Stock Voucher - ' . $voucher->voucher_number)

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

    /* Status Badge Styling */
    .status-badge-large {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* Info Cards */
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 0 0 0 15px;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .info-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 0;
    }

    /* Enhanced Table Styling */
    .enhanced-table {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .enhanced-table th {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
    }

    .enhanced-table td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f1f3f4;
    }

    .enhanced-table tbody tr {
        transition: all 0.2s ease;
    }

    .enhanced-table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.005);
    }

    /* Difference Indicators */
    .difference-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    /* Action Buttons */
    .action-btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-group .action-btn {
        margin-right: 0.5rem;
    }

    /* Summary Section */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .summary-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .summary-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .summary-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .summary-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }

        .info-card {
            padding: 1rem;
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
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-0 fw-bold">{{ $voucher->voucher_number }}</h1>
                        <p class="mb-0 opacity-75">Physical Stock Voucher Details</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <nav aria-label="breadcrumb" class="me-3">
                        <ol class="breadcrumb mb-0" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                            <li class="breadcrumb-item">
                                <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}" class="text-white-50">Physical Stock</a>
                            </li>
                            <li class="breadcrumb-item active text-white">{{ $voucher->voucher_number }}</li>
                        </ol>
                    </nav>
                    <span class="status-badge-large bg-{{ $voucher->status_color }} text-white">
                        @if($voucher->status === 'draft')
                            <i class="fas fa-edit me-2"></i>
                        @elseif($voucher->status === 'pending')
                            <i class="fas fa-clock me-2"></i>
                        @elseif($voucher->status === 'approved')
                            <i class="fas fa-check me-2"></i>
                        @else
                            <i class="fas fa-times me-2"></i>
                        @endif
                        {{ $voucher->status_display }}
                    </span>
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group me-2">
                    @if($voucher->canEdit())
                        <a href="{{ route('tenant.inventory.physical-stock.edit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                           class="btn btn-light action-btn">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    @endif

                    @if($voucher->status === 'draft')
                        <form method="POST" action="{{ route('tenant.inventory.physical-stock.submit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning action-btn">
                                <i class="fas fa-paper-plane me-2"></i>Submit for Approval
                            </button>
                        </form>
                    @endif

                    @if($voucher->canApprove())
                        <form method="POST" action="{{ route('tenant.inventory.physical-stock.approve', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success action-btn" onclick="return confirm('Are you sure you want to approve this voucher? This will create stock movements.')">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                        </form>
                    @endif
                </div>

                <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
                   class="btn btn-light action-btn">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Voucher Details -->
        <div class="col-lg-8">
            <!-- Enhanced Header Information -->
            <div class="card modern-card shadow mb-4">
                <div class="card-header bg-transparent py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h6 class="m-0 font-weight-bold text-dark">Voucher Information</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">📋 Voucher Number</div>
                                <div class="info-value text-primary">{{ $voucher->voucher_number }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">📅 Voucher Date</div>
                                <div class="info-value">{{ $voucher->voucher_date->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">🏷️ Reference Number</div>
                                <div class="info-value">{{ $voucher->reference_number ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">⚖️ Adjustment Type</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $voucher->adjustment_type === 'shortage' ? 'danger' : ($voucher->adjustment_type === 'excess' ? 'success' : 'warning') }} text-white p-2 rounded-3">
                                        @if($voucher->adjustment_type === 'shortage')
                                            <i class="fas fa-arrow-down me-1"></i>
                                        @elseif($voucher->adjustment_type === 'excess')
                                            <i class="fas fa-arrow-up me-1"></i>
                                        @else
                                            <i class="fas fa-exchange-alt me-1"></i>
                                        @endif
                                        {{ $voucher->adjustment_type_display }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">📦 Total Items</div>
                                <div class="info-value">{{ $voucher->total_items }} items</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">💰 Total Adjustments</div>
                                <div class="info-value text-info">₦{{ number_format($voucher->total_adjustments, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($voucher->remarks)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="info-card">
                                    <div class="info-label">📝 Remarks</div>
                                    <div class="info-value" style="font-size: 1rem; font-style: italic;">{{ $voucher->remarks }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Product Entries -->
            <div class="card modern-card shadow">
                <div class="card-header bg-transparent py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h6 class="m-0 font-weight-bold text-dark">Product Entries</h6>
                    </div>
                </div>
                <div class="card-body">
                    @if($voucher->entries->count() > 0)
                        <div class="table-responsive">
                            <table class="table enhanced-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Details</th>
                                        <th>Book Qty</th>
                                        <th>Physical Qty</th>
                                        <th>Difference</th>
                                        <th>Rate</th>
                                        <th>Value Impact</th>
                                        <th>Additional Info</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($voucher->entries as $index => $entry)
                                        <tr class="{{ $entry->hasDifference() ? ($entry->isExcess() ? 'table-light border-start border-success border-3' : 'table-light border-start border-danger border-3') : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 30px; height: 30px; font-size: 0.875rem; font-weight: 600;">
                                                    {{ $index + 1 }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-box" style="font-size: 0.75rem;"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="text-dark">{{ $entry->product->name }}</strong>
                                                        @if($entry->product->sku)
                                                            <br><small class="text-muted">
                                                                <i class="fas fa-barcode me-1"></i>{{ $entry->product->sku }}
                                                            </small>
                                                        @endif
                                                        @if($entry->product->category)
                                                            <br><span class="badge bg-info rounded-pill">{{ $entry->product->category->name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-primary">{{ number_format($entry->book_quantity, 4) }}</div>
                                                    @if($entry->product->primaryUnit)
                                                        <small class="text-muted">{{ $entry->product->primaryUnit->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-success">{{ number_format($entry->physical_quantity, 4) }}</div>
                                                    <small class="text-muted">Counted</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    @if($entry->difference_quantity > 0)
                                                        <span class="difference-badge difference-positive">
                                                            <i class="fas fa-arrow-up me-1"></i>
                                                            +{{ number_format($entry->difference_quantity, 4) }}
                                                        </span>
                                                    @elseif($entry->difference_quantity < 0)
                                                        <span class="difference-badge difference-negative">
                                                            <i class="fas fa-arrow-down me-1"></i>
                                                            {{ number_format($entry->difference_quantity, 4) }}
                                                        </span>
                                                    @else
                                                        <span class="difference-badge difference-zero">
                                                            <i class="fas fa-minus me-1"></i>
                                                            0.0000
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold text-info">₦{{ number_format($entry->current_rate, 2) }}</div>
                                                    <small class="text-muted">Per unit</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    @if($entry->difference_value != 0)
                                                        <div class="fw-bold {{ $entry->difference_value > 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $entry->difference_value > 0 ? '+' : '' }}₦{{ number_format($entry->difference_value, 2) }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $entry->difference_value > 0 ? 'Excess' : 'Shortage' }}
                                                        </small>
                                                    @else
                                                        <div class="text-muted">₦0.00</div>
                                                        <small class="text-muted">No change</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($entry->batch_number)
                                                    <small><strong>Batch:</strong> {{ $entry->batch_number }}</small><br>
                                                @endif
                                                @if($entry->location)
                                                    <small><strong>Location:</strong> {{ $entry->location }}</small><br>
                                                @endif
                                                @if($entry->expiry_date)
                                                    <small><strong>Expires:</strong> {{ $entry->expiry_date->format('d M Y') }}</small><br>
                                                @endif
                                                @if($entry->remarks)
                                                    <small class="text-muted">{{ $entry->remarks }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Row -->
                        <div class="border-top pt-3 mt-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <strong>Summary:</strong>
                                </div>
                                <div class="col-md-4 text-end">
                                    <strong>Total: ₦{{ number_format($voucher->total_adjustments, 2) }}</strong>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small class="text-success">
                                        Excess: ₦{{ number_format($voucher->entries->where('difference_quantity', '>', 0)->sum(function($e) { return $e->absolute_difference_value; }), 2) }}
                                    </small>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-danger">
                                        Shortage: ₦{{ number_format($voucher->entries->where('difference_quantity', '<', 0)->sum(function($e) { return $e->absolute_difference_value; }), 2) }}
                                    </small>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-info">
                                        Net: ₦{{ number_format($voucher->entries->sum('difference_value'), 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No entries found for this voucher.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status & Actions</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <div class="info-label mb-2">Current Status</div>
                        <span class="status-badge-large bg-{{ $voucher->status_color }}">{{ $voucher->status_display }}</span>
                    </div>

                    @if($voucher->status === 'draft')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This voucher is in draft status. You can edit it or submit for approval.
                        </div>
                    @elseif($voucher->status === 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            This voucher is pending approval.
                        </div>
                    @elseif($voucher->status === 'approved')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            This voucher has been approved and stock movements have been created.
                        </div>
                    @elseif($voucher->status === 'cancelled')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            This voucher has been cancelled.
                        </div>
                    @endif

                    <!-- Enhanced Action Buttons -->
                    <div class="d-grid gap-2">
                        @if($voucher->canEdit())
                            <a href="{{ route('tenant.inventory.physical-stock.edit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                               class="action-btn btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Voucher
                            </a>
                        @endif

                        @if($voucher->status === 'draft')
                            <form method="POST" action="{{ route('tenant.inventory.physical-stock.submit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                                @csrf
                                <button type="submit" class="action-btn btn btn-warning w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Submit for Approval
                                </button>
                            </form>
                        @endif

                        @if($voucher->canApprove())
                            <form method="POST" action="{{ route('tenant.inventory.physical-stock.approve', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                                @csrf
                                <button type="submit" class="action-btn btn btn-success w-100"
                                        onclick="return confirm('Are you sure you want to approve this voucher? This will create stock movements.')">
                                    <i class="fas fa-check me-2"></i>Approve Voucher
                                </button>
                            </form>
                        @endif

                        @if(in_array($voucher->status, ['draft', 'pending']))
                            <form method="POST" action="{{ route('tenant.inventory.physical-stock.cancel', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                                @csrf
                                <button type="submit" class="action-btn btn btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to cancel this voucher?')">
                                    <i class="fas fa-ban me-2"></i>Cancel Voucher
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Audit Information -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Audit Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <strong>Created By:</strong><br>
                            {{ $voucher->creator->name ?? 'System' }}<br>
                            <small class="text-muted">{{ $voucher->created_at->format('d M Y, g:i A') }}</small>
                        </div>

                        @if($voucher->updater && $voucher->updated_at != $voucher->created_at)
                            <div class="col-12 mb-3">
                                <strong>Last Updated By:</strong><br>
                                {{ $voucher->updater->name }}<br>
                                <small class="text-muted">{{ $voucher->updated_at->format('d M Y, g:i A') }}</small>
                            </div>
                        @endif

                        @if($voucher->approver)
                            <div class="col-12 mb-3">
                                <strong>Approved By:</strong><br>
                                {{ $voucher->approver->name }}<br>
                                <small class="text-muted">{{ $voucher->approved_at->format('d M Y, g:i A') }}</small>
                            </div>
                        @endif
                    </div>

                    @if($voucher->status === 'approved')
                        <hr>
                        <div class="text-center">
                            <a href="{{ route('tenant.inventory.products.stock-movements', ['tenant' => $tenant->slug]) }}?transaction_type=physical_adjustment&transaction_reference={{ $voucher->voucher_number }}"
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-history me-1"></i>View Stock Movements
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
