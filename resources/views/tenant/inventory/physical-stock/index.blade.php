@extends('layouts.tenant')

@section('title', 'Physical Stock Vouchers')

@push('styles')
<style>
    .stats-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--bs-primary);
    }

    .stats-card.border-left-warning::before { background: var(--bs-warning); }
    .stats-card.border-left-success::before { background: var(--bs-success); }
    .stats-card.border-left-info::before { background: var(--bs-info); }
    .stats-card.border-left-danger::before { background: var(--bs-danger); }

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

    .filters-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .table-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .voucher-table th {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem;
    }

    .voucher-table td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f1f3f4;
    }

    .voucher-table tbody tr {
        transition: all 0.2s ease;
    }

    .voucher-table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .voucher-number {
        font-weight: 600;
        font-size: 1.1em;
        background: linear-gradient(45deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .status-badge {
        font-size: 0.85em;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .adjustment-badge {
        font-size: 0.85em;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .action-dropdown .dropdown-toggle {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        border: 2px solid #e9ecef;
        background: white;
        transition: all 0.3s ease;
    }

    .action-dropdown .dropdown-toggle:hover {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        margin: 2rem 0;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #6c757d;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .filter-toggle-btn {
        border-radius: 20px;
        border: 2px solid #e9ecef;
        background: white;
        transition: all 0.3s ease;
    }

    .filter-toggle-btn:hover {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }

    .create-btn {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
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
                        <h1 class="h2 mb-0 fw-bold">Physical Stock Vouchers</h1>
                        <p class="mb-0 opacity-75">Manage physical stock adjustments and reconciliation with precision</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('tenant.inventory.physical-stock.create', ['tenant' => $tenant->slug]) }}"
                   class="btn btn-light create-btn text-primary fw-bold">
                    <i class="fas fa-plus me-2"></i>New Physical Stock Voucher
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-primary shadow h-100">
                <div class="card-body p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-icon bg-primary text-white mb-3">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-2 tracking-wide">
                                Total Vouchers
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_vouchers']) }}</div>
                            <div class="text-xs text-muted mt-1">All time records</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-warning shadow h-100">
                <div class="card-body p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-icon bg-warning text-white mb-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-2 tracking-wide">
                                Pending Approval
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_approval']) }}</div>
                            <div class="text-xs text-muted mt-1">Awaiting review</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-success shadow h-100">
                <div class="card-body p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-icon bg-success text-white mb-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-2 tracking-wide">
                                Approved This Month
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['approved_this_month']) }}</div>
                            <div class="text-xs text-muted mt-1">{{ now()->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-info shadow h-100">
                <div class="card-body p-4">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-icon bg-info text-white mb-3">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-2 tracking-wide">
                                Adjustments This Month
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">₦{{ number_format($stats['total_adjustments_this_month'], 2) }}</div>
                            <div class="text-xs text-muted mt-1">Value adjusted</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="card filters-card shadow mb-4">
        <div class="card-header bg-transparent py-4 d-flex justify-content-between align-items-center border-0">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-primary text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                    <i class="fas fa-filter"></i>
                </div>
                <h6 class="m-0 font-weight-bold text-dark">Advanced Filters</h6>
            </div>
            <button class="btn filter-toggle-btn btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                <i class="fas fa-sliders-h me-2"></i>Toggle Filters
            </button>
        </div>
        <div class="collapse" id="filtersCollapse">
            <div class="card-body pt-0">
                <form method="GET" action="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark">Status</label>
                            <select name="status" class="form-select rounded-3 border-2">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>📝 Draft</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark">Adjustment Type</label>
                            <select name="adjustment_type" class="form-select rounded-3 border-2">
                                <option value="">All Types</option>
                                <option value="shortage" {{ request('adjustment_type') === 'shortage' ? 'selected' : '' }}>📉 Shortage</option>
                                <option value="excess" {{ request('adjustment_type') === 'excess' ? 'selected' : '' }}>📈 Excess</option>
                                <option value="mixed" {{ request('adjustment_type') === 'mixed' ? 'selected' : '' }}>🔄 Mixed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-dark">From Date</label>
                            <input type="date" name="from_date" class="form-control rounded-3 border-2" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-dark">To Date</label>
                            <input type="date" name="to_date" class="form-control rounded-3 border-2" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-dark">Search</label>
                            <input type="text" name="search" class="form-control rounded-3 border-2" placeholder="Voucher number..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col">
                            <button type="submit" class="btn btn-primary rounded-3 me-3 px-4">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}" class="btn btn-outline-secondary rounded-3 px-4">
                                <i class="fas fa-times me-2"></i>Clear All
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Vouchers Table -->
    <div class="card table-card shadow">
        <div class="card-header bg-transparent py-4 border-0">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-primary text-white me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                    <i class="fas fa-table"></i>
                </div>
                <h6 class="m-0 font-weight-bold text-dark">Physical Stock Vouchers</h6>
            </div>
        </div>
        <div class="card-body p-0">
            @if($vouchers->count() > 0)
                <div class="table-responsive">
                    <table class="table voucher-table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="border-0">Voucher Details</th>
                                <th class="border-0">Date & Reference</th>
                                <th class="border-0">Items & Type</th>
                                <th class="border-0">Adjustments</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Created By</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="{{ route('tenant.inventory.physical-stock.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                                                   class="voucher-number text-decoration-none">
                                                    {{ $voucher->voucher_number }}
                                                </a>
                                                <div class="text-muted small mt-1">
                                                    ID: #{{ $voucher->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $voucher->voucher_date->format('d M Y') }}</div>
                                        <div class="text-muted small">
                                            {{ $voucher->reference_number ?? 'No reference' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-info rounded-pill mb-2 align-self-start">
                                                <i class="fas fa-boxes me-1"></i>{{ $voucher->total_items }} items
                                            </span>
                                            <span class="adjustment-badge bg-{{ $voucher->adjustment_type === 'shortage' ? 'danger' : ($voucher->adjustment_type === 'excess' ? 'success' : 'warning') }} text-white align-self-start">
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
                                    </td>
                                    <td>
                                        <div class="fw-bold text-info h5 mb-0">
                                            ₦{{ number_format($voucher->total_adjustments, 2) }}
                                        </div>
                                        <div class="text-muted small">Total value</div>
                                    </td>
                                    <td>
                                        <span class="status-badge bg-{{ $voucher->status_color }} text-white">
                                            @if($voucher->status === 'draft')
                                                <i class="fas fa-edit me-1"></i>
                                            @elseif($voucher->status === 'pending')
                                                <i class="fas fa-clock me-1"></i>
                                            @elseif($voucher->status === 'approved')
                                                <i class="fas fa-check me-1"></i>
                                            @else
                                                <i class="fas fa-times me-1"></i>
                                            @endif
                                            {{ $voucher->status_display }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $voucher->creator->name ?? 'System' }}</div>
                                                <div class="text-muted small">{{ $voucher->created_at->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-sm dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog me-1"></i>Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('tenant.inventory.physical-stock.show', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                                                        <i class="fas fa-eye me-2 text-info"></i>View Details
                                                    </a>
                                                </li>
                                                @if($voucher->canEdit())
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('tenant.inventory.physical-stock.edit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                                                            <i class="fas fa-edit me-2 text-warning"></i>Edit Voucher
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($voucher->status === 'draft')
                                                    <li>
                                                        <form method="POST"
                                                              action="{{ route('tenant.inventory.physical-stock.submit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-paper-plane me-2 text-primary"></i>Submit for Approval
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($voucher->canApprove())
                                                    <li>
                                                        <form method="POST"
                                                              action="{{ route('tenant.inventory.physical-stock.approve', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-check me-2 text-success"></i>Approve Voucher
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($voucher->status !== 'approved')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST"
                                                              action="{{ route('tenant.inventory.physical-stock.destroy', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this voucher?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>Delete Voucher
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Pagination -->
                <div class="d-flex justify-content-between align-items-center p-4 bg-light">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Showing {{ $vouchers->firstItem() }} to {{ $vouchers->lastItem() }} of {{ $vouchers->total() }} results
                    </div>
                    <div>
                        {{ $vouchers->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Physical Stock Vouchers Found</h4>
                    <p class="text-muted mb-4">Start managing your inventory by creating your first physical stock voucher.</p>
                    <a href="{{ route('tenant.inventory.physical-stock.create', ['tenant' => $tenant->slug]) }}" class="btn btn-primary btn-lg rounded-3">
                        <i class="fas fa-plus me-2"></i>Create Your First Voucher
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add some interactive functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats cards on load
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';

                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });

        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('.voucher-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });

            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Add loading state to form submissions
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    submitBtn.disabled = true;

                    // Re-enable after 3 seconds if still on page
                    setTimeout(() => {
                        if (document.contains(submitBtn)) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    }, 3000);
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, 5000);
        });

        // Add smooth scrolling to anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>
@endpush
