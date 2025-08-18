@extends('layouts.tenant')

@section('title', $voucherType->name . ' - Voucher Type Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full {{ $voucherType->is_system_defined ? 'bg-blue-100' : 'bg-purple-100' }} flex items-center justify-center">
                    <span class="text-lg font-bold {{ $voucherType->is_system_defined ? 'text-blue-800' : 'text-purple-800' }}">
                        {{ $voucherType->abbreviation }}
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $voucherType->name }}</h1>
                    <p class="text-gray-600">Code: <span class="font-mono">{{ $voucherType->code }}</span></p>
                </div>
            </div>
        </div>

               <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-left">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                          <dd class="text-lg font-medium text-gray-900">
                                @if($voucherType->is_system_defined)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        System
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Custom
                                    </span>
                                @endif
                            </dd>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Voucher Type Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $voucherType->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $voucherType->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Abbreviation</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $voucherType->abbreviation }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1">
                                @if($voucherType->is_system_defined)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        System Defined
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Custom
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if($voucherType->description)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $voucherType->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Numbering Configuration -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Numbering Configuration</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Numbering Method</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $voucherType->numbering_method === 'auto' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($voucherType->numbering_method) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Starting Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $voucherType->starting_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $voucherType->current_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Next Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ $voucherType->prefix }}{{ str_pad($voucherType->current_number + 1, 4, '0', STR_PAD_LEFT) }}
                            </dd>
                        </div>
                        @if($voucherType->prefix)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Prefix</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $voucherType->prefix }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Features & Capabilities</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-center">
                            @if($voucherType->has_reference)
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">Requires Reference</span>
                        </div>

                        <div class="flex items-center">
                            @if($voucherType->affects_inventory)
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">Affects Inventory</span>
                        </div>

                        <div class="flex items-center">
                            @if($voucherType->affects_cashbank)
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">Affects Cash/Bank</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($voucherType->is_active)
                        <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug, 'type' => $voucherType->id]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Voucher
                        </a>
                    @endif

                    @if(!$voucherType->is_system_defined)
                        <a href="{{ route('tenant.accounting.voucher-types.edit',['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Voucher Type
                        </a>
                    @endif

                    <form action="{{ route('tenant.accounting.voucher-types.toggle', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                          method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg {{ $voucherType->is_active ? 'text-red-700 hover:bg-red-50' : 'text-green-700 hover:bg-green-50' }} bg-white">
                            @if($voucherType->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                                Deactivate
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate
                            @endif
                        </button>
                    </form>

                    @if(!$voucherType->is_system_defined && $voucherCount === 0)
                        <form action="{{ route('tenant.accounting.voucher-types.destroy',['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this voucher type? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Numbering Reset -->
            @if(!$voucherType->is_system_defined && $voucherType->numbering_method === 'auto')
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reset Numbering</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Reset the numbering sequence for this voucher type. This will set the next voucher number to your specified value.
                    </p>
                    <form action="{{ route('tenant.accounting.voucher-types.reset-numbering', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                          method="POST"
                          x-data="{ resetNumber: {{ $voucherType->current_number + 1 }} }"
                          onsubmit="return confirm('Are you sure you want to reset the numbering? This action cannot be undone.')">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="reset_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Reset to Number
                            </label>
                            <input type="number"
                                   name="reset_number"
                                   id="reset_number"
                                   x-model="resetNumber"
                                   min="1"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Next voucher will be: <span class="font-mono" x-text="'{{ $voucherType->prefix }}' + String(resetNumber).padStart(4, '0')"></span>
                            </p>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-orange-300 text-sm font-medium rounded-lg text-orange-700 bg-white hover:bg-orange-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Numbering
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Recent Vouchers -->
            @if($recentVouchers->count() > 0)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Vouchers</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($recentVouchers as $voucher)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900 font-mono">{{ $voucher->voucher_number }}</p>
                                <p class="text-xs text-gray-500">{{ $voucher->created_at->format('M j, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">₦{{ number_format($voucher->total_amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $voucher->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($voucher->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug, 'type' => $voucherType->id]) }}"
                           class="text-sm text-primary-600 hover:text-primary-500">
                            View all vouchers →
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Metadata</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $voucherType->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $voucherType->updated_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        @if($voucherType->default_accounts)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Default Accounts</dt>
                            <dd class="mt-1">
                                <div class="space-y-1">
                                    @if($voucherType->default_accounts && is_array($voucherType->default_accounts))
                                        @foreach($voucherType->default_accounts as $accountType => $accountId)
                                            <div class="flex justify-between py-2">
            <span class="text-sm text-gray-600">
                {{ ucfirst(str_replace('_', ' ', $accountType)) }}:
            </span>
            <span class="text-sm font-medium text-gray-900">
                {{ $accountId }}
            </span>
        </div>
    @endforeach
@else
    <p class="text-sm text-gray-500">No default accounts configured</p>
@endif
                                </div>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                </div>